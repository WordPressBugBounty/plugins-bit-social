<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Helpers\Arr;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Services\AutoPost\AutoPostRuleService;
use BitApps\Social\HTTP\Services\Social\Social;
use BitApps\Social\Model\Account;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\LogRun;
use BitApps\SocialPro\HTTP\Services\AutoPost\AutoPostService;
use BitApps\SocialPro\Model\GroupsAccount;
use Throwable;
use WP_Post;

class AutoPostController
{
    use Common;

    /**
     * Cron hook of the previous build, which re-ran auto-post a minute after
     * every publish that had a taxonomy filter in play. Still handled so events
     * queued just before the update are not dropped; see publishAfterSettle().
     */
    public const SETTLE_HOOK = Config::VAR_PREFIX . 'autopost_settle';

    /**
     * Safety-net cron hook for a block editor publish whose meta-box save never
     * arrives. See settleQueued().
     */
    public const FINALIZE_HOOK = Config::VAR_PREFIX . 'autopost_finalize';

    /**
     * Post meta marking a published post that has not been auto-posted yet. Its
     * value is the WP-local timestamp of the publish, which is where a rule's
     * delay counts from.
     */
    public const PENDING_META = '_' . Config::VAR_PREFIX . 'autopost_pending';

    /**
     * Option remembering, per post type, whether the block editor shows classic
     * meta boxes — and therefore saves them in a request of its own.
     */
    public const EDITOR_META_BOX_OPTION = 'editor_meta_box_post_types';

    /**
     * How long a block editor publish waits for its meta-box save before
     * auto-posting without it.
     */
    private const META_BOX_SAVE_TIMEOUT = 30;

    /**
     * Only the master switch and the log setting are site-wide now; everything
     * else — post types, taxonomy filter, accounts, delay, post content —
     * belongs to an individual rule. See AutoPostRuleService.
     */
    public $defaultAutoPostSettings = [
        'isEnabled' => false,
        'keepLogs'  => true,
        'rules'     => []
    ];

    /**
     * Posts published during this request, waiting for it to finish: post id =>
     * whether the block editor's meta-box save is still to come.
     *
     * @var array<int, bool>
     */
    private static $queued = [];

    public function autoPostSettings()
    {
        $settings = $this->getAutoPostSettings();

        // Between the Free and the Pro update of this release, the admin app on
        // screen is a Pro build from before rules, which blanks its Auto Post
        // screen on the keys this shape no longer carries. Give it the shape it
        // can read; storage stays in the rules shape either way. Temporary —
        // see AutoPostRuleService::RULES_RELEASE.
        if (AutoPostRuleService::needsLegacyView()) {
            return Response::success(AutoPostRuleService::legacyView($settings));
        }

        return Response::success($settings);
    }

    public function update(Request $request)
    {
        $incoming = (array) $request->autoPostSettings;

        // The same build saving back what legacyView() handed it: fold it into
        // rule one rather than letting a payload with no `rules` key wipe them.
        $settings = !isset($incoming['rules']) && AutoPostRuleService::needsLegacyView()
            ? AutoPostRuleService::applyLegacyUpdate(Config::getOption('auto_post_settings'), $incoming)
            : AutoPostRuleService::normalizeSettings($incoming);

        Config::updateOption('auto_post_settings', $settings);

        return Response::success('Auto post settings updated.');
    }

    public function getAutoPostSettings(): array
    {
        $autoPostSettings = Config::getOption('auto_post_settings');

        if (!$autoPostSettings) {
            Config::updateOption('auto_post_settings', $this->defaultAutoPostSettings);

            $autoPostSettings = $this->defaultAutoPostSettings;
        }
        $keyDiff = $this->arrayDiffNestedKeys($this->defaultAutoPostSettings, $autoPostSettings);

        if (\count($keyDiff) !== 0) {
            $autoPostSettings = array_replace_recursive($autoPostSettings, $keyDiff);

            Config::updateOption('auto_post_settings', $autoPostSettings);
        }

        $normalized = AutoPostRuleService::normalizeSettings($autoPostSettings);

        // An install that predates rules is rewritten once, so the single
        // configuration it was set up with lives on as rule one instead of
        // being migrated again on every read.
        if ($normalized !== $autoPostSettings) {
            Config::updateOption('auto_post_settings', $normalized);
        }

        return $normalized;
    }

    /**
     * Marks a freshly published post for auto-posting. Nothing is shared from
     * here: this hook runs before the post's data is complete.
     *
     * Plugins write custom fields and terms after it — later on this hook, at
     * the end of an import or form submission, or, in the block editor, in the
     * meta-box request that follows the save (Meta Box, ACF, taxonomies not in
     * the REST API). Sharing now reads empty custom fields and has taxonomy
     * filters skip posts that do carry the term. So the post is shared once
     * that data has landed: when this request ends, or when the editor's
     * meta-box save ends. See settleQueued().
     *
     * @param int          $postId
     * @param WP_Post      $post
     * @param bool         $update
     * @param null|WP_Post $postBefore
     */
    public function publishWpPost($postId, $post, $update, $postBefore)
    {
        if ('publish' !== $post->post_status) {
            return;
        }

        if ($postBefore && 'publish' === $postBefore->post_status) {
            // Saving a post that is still waiting — the block editor's
            // meta-box request — means what it waited for is now written.
            if ($this->isAwaitingFollowUpSave($postId)) {
                $this->queue($postId, false);
            }

            return;
        }

        if (!$this->getAutoPostSettings()['isEnabled']) {
            return;
        }

        if (!$this->publisherCanAutoPost($post)) {
            return;
        }

        update_post_meta($postId, self::PENDING_META, current_time('timestamp'));

        $this->queue($postId, $this->awaitsMetaBoxSave($post));
    }

    /**
     * Shutdown callback: auto-posts what this request published, now that
     * everything it was going to write about those posts is written.
     *
     * A block editor publish with meta boxes on screen is the exception — its
     * custom fields arrive in the next request, whose own shutdown picks the
     * post up. A cron event stands in case that request never comes.
     */
    public function settleQueued()
    {
        $queued = self::$queued;
        self::$queued = [];

        foreach ($queued as $postId => $awaitsMetaBoxSave) {
            if (!$awaitsMetaBoxSave) {
                $this->finalizePendingAutoPost($postId);

                continue;
            }

            if (!wp_next_scheduled(self::FINALIZE_HOOK, [$postId])) {
                wp_schedule_single_event(time() + self::META_BOX_SAVE_TIMEOUT, self::FINALIZE_HOOK, [$postId]);
            }
        }
    }

    /**
     * Auto-posts a pending post, once.
     *
     * The request that published it, the meta-box save and the safety-net event
     * can each get here; only the one whose delete of the pending marker lands
     * goes on to share, so the post is never shared twice.
     *
     * @param int $postId
     */
    public function finalizePendingAutoPost($postId)
    {
        $postId = absint($postId);
        $publishedAt = (int) get_post_meta($postId, self::PENDING_META, true);

        if (!delete_post_meta($postId, self::PENDING_META)) {
            return;
        }

        wp_clear_scheduled_hook(self::FINALIZE_HOOK, [$postId]);

        $post = get_post($postId);

        if (!$post || 'publish' !== $post->post_status) {
            return;
        }

        $autoPostSettings = $this->getAutoPostSettings();

        if (!$autoPostSettings['isEnabled']) {
            return;
        }

        $this->dispatchAutoPostRules($post, $postId, $autoPostSettings, $publishedAt ?: null);
    }

    /**
     * Callback for SETTLE_HOOK events queued by the previous build. Publishing
     * no longer queues them; remove once no install can still hold one.
     *
     * @param int $postId
     */
    public function publishAfterSettle($postId)
    {
        $post = get_post(absint($postId));

        if (!$post || 'publish' !== $post->post_status) {
            return;
        }

        $autoPostSettings = $this->getAutoPostSettings();

        if (!$autoPostSettings['isEnabled'] || !$this->publisherCanAutoPost($post)) {
            return;
        }

        $this->dispatchAutoPostRules($post, $post->ID, $autoPostSettings);
    }

    /**
     * Remembers whether the block editor shows classic meta boxes for this post
     * type. The editor saves those in a second request after every save, and
     * awaitsMetaBoxSave() needs to know whether one is coming.
     *
     * Recorded as the editor loads — always before that editor can publish —
     * and written only when the answer changes.
     *
     * @param array $wpMetaBoxes
     *
     * @return array unchanged
     */
    public function rememberEditorMetaBoxes($wpMetaBoxes)
    {
        global $post, $current_screen;

        if (!$post instanceof WP_Post || !$current_screen || !\is_array($wpMetaBoxes)) {
            return $wpMetaBoxes;
        }

        $hasMetaBoxes = $this->hasEditorMetaBoxes(
            isset($wpMetaBoxes[$current_screen->id]) ? (array) $wpMetaBoxes[$current_screen->id] : []
        );

        $postTypes = (array) Config::getOption(self::EDITOR_META_BOX_OPTION, []);

        if (!isset($postTypes[$post->post_type]) || $postTypes[$post->post_type] !== $hasMetaBoxes) {
            $postTypes[$post->post_type] = $hasMetaBoxes;

            Config::updateOption(self::EDITOR_META_BOX_OPTION, $postTypes);
        }

        return $wpMetaBoxes;
    }

    /**
     * Schedules the rules that wait, and shares the rest now.
     *
     * @param WP_Post  $post
     * @param int      $postId
     * @param array    $autoPostSettings
     * @param null|int $publishedAt      WP-local timestamp delays count from; now when omitted
     */
    public function dispatchAutoPostRules($post, $postId, $autoPostSettings, $publishedAt = null)
    {
        $rules = AutoPostRuleService::rulesForPost($autoPostSettings, $post);

        if (empty($rules)) {
            return;
        }

        // Delay is a per-rule setting, so one post can both schedule some of
        // its rules for later and share the rest right away.
        $hasImmediateRule = false;

        foreach ($rules as $rule) {
            if (AutoPostRuleService::isDelayed($rule) && class_exists('BitApps\SocialPro\HTTP\Services\AutoPost\AutoPostService')) {
                // The delay counts from the publish, not from whenever the
                // post's data finished landing.
                AutoPostService::createAutoPostDelay($post, $postId, $rule, $autoPostSettings, $publishedAt);

                continue;
            }

            $hasImmediateRule = true;
        }

        if (!$hasImmediateRule) {
            return;
        }

        if (!$this->canShareInBackground($postId)) {
            $this->shareImmediateRules($postId);

            return;
        }

        $args = [
            'timeout'   => 0.1,
            'blocking'  => false,
            'cookies'   => $this->sanitizeRequestCookies(),
            'sslverify' => apply_filters('https_local_ssl_verify', false), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This is a WordPress core filter.
        ];

        $queryArgs = [
            'action'      => Config::VAR_PREFIX . 'auto-post-background-process',
            '_ajax_nonce' => wp_create_nonce(Config::withPrefix('nonce')),
        ];

        $url = add_query_arg($queryArgs, admin_url('admin-ajax.php'));

        (new HttpClient())->request($url, 'POST', ['post_id' => $postId], null, $args);
    }

    /**
     * The background request dispatchAutoPostRules() sends for immediate rules.
     */
    public function executeSocialPost()
    {
        check_ajax_referer(Config::withPrefix('nonce'));

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Insufficient permission.'], 403);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is validated at the start of this method.
        $postId = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;

        if (0 === $postId) {
            wp_send_json_error(['message' => 'Invalid post ID.'], 400);
        }

        if (!current_user_can('edit_post', $postId)) {
            wp_send_json_error(['message' => 'You are not allowed to access this post.'], 403);
        }

        if (!get_post($postId)) {
            wp_send_json_error(['message' => 'Post not found.'], 404);
        }

        wp_send_json_success($this->shareImmediateRules($postId));
    }

    public function groupsAccountIds($groupIds)
    {
        if (class_exists('BitApps\SocialPro\Model\GroupsAccount')) {
            $arr = new Arr();
            $accountIds = GroupsAccount::where('group_id', $groupIds)->get('account_id');

            return $arr->pluck($accountIds, 'account_id');
        }

        return [];
    }

    public function arrayDiffNestedKeys($array1, $array2)
    {
        $diff = [];
        foreach ($array1 as $key => $value) {
            if (!\array_key_exists($key, $array2)) {
                $diff[$key] = $value;
            } elseif (\is_array($value) && \is_array($array2[$key])) {
                $nestedDiff = $this->arrayDiffNestedKeys($value, $array2[$key]);
                if (!empty($nestedDiff)) {
                    $diff[$key] = $nestedDiff;
                }
            }
        }

        return $diff;
    }

    /**
     * Whether this request is the block editor saving a post. Its REST client
     * tags every request with _locale=user; a REST publish from anything else
     * has no meta-box save coming.
     *
     * @return bool
     */
    protected function isBlockEditorSave()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only request classification.
        return \defined('REST_REQUEST') && REST_REQUEST && isset($_GET['_locale']) && 'user' === $_GET['_locale'];
    }

    /**
     * Shares a post through every matching rule that has no delay.
     *
     * @param int $postId
     *
     * @return array one result per account
     */
    private function shareImmediateRules($postId)
    {
        $templates = (array) (new SocialTemplateController())->getSocialTemplates();

        $autoPostSettings = $this->getAutoPostSettings();

        $rules = AutoPostRuleService::rulesForPost($autoPostSettings, get_post($postId));

        $publishPostData = [];

        // One auto-post is one run. Without this every account logs under an id
        // of its own — LogRun::current() mints one per write when nothing is
        // open — and the Logs screen shows a five-account auto-post as five
        // separate events. A scheduled run gets this from SocialExecution;
        // auto-post publishes here instead and had no equivalent.
        LogRun::start();

        try {
            foreach ($rules as $rule) {
                // Delayed rules were turned into schedules when the post was
                // published; only the immediate ones are shared here.
                if (AutoPostRuleService::isDelayed($rule)) {
                    continue;
                }

                $accounts = $this->ruleAccounts($rule, $postId);
                $ruleTemplates = AutoPostRuleService::templatesFor($rule, $templates);

                foreach ($accounts as $account) {
                    $isPlatFormExists = $this->isPlatFormExists($account->id);

                    if (!$isPlatFormExists) {
                        continue;
                    }

                    $platform = new Social(new $isPlatFormExists['class']());

                    $platformName = $isPlatFormExists['platform'];

                    if (!isset($ruleTemplates[$platformName])) {
                        continue;
                    }

                    $data = [
                        'post'            => (array) get_post($postId),
                        'template'        => $ruleTemplates[$platformName],
                        'account_details' => $isPlatFormExists['details'],
                        'keepLogs'        => $autoPostSettings['keepLogs']
                    ];

                    try {
                        $publishPostData[] = $platform->publishPost($data);
                    } catch (Throwable $e) {
                        $publishPostData[] = ['status' => 'error', 'message' => $e->getMessage()];
                    }
                }
            }
        } finally {
            LogRun::end();
        }

        Hooks::doAction(Config::withPrefix('all_platforms_post_publish'), $publishPostData);

        return $publishPostData;
    }

    /**
     * Whether a save of this published post is the follow-up its auto-post is
     * waiting for. The meta-box save arrives within seconds of the publish; a
     * marker older than a few minutes belongs to the safety-net event, and an
     * edit made long after publishing must not be what shares the post.
     *
     * @param int $postId
     *
     * @return bool
     */
    private function isAwaitingFollowUpSave($postId)
    {
        $publishedAt = (int) get_post_meta($postId, self::PENDING_META, true);

        return $publishedAt > 0 && current_time('timestamp') - $publishedAt <= 10 * MINUTE_IN_SECONDS;
    }

    /**
     * Holds a post until this request ends. See settleQueued().
     *
     * @param int  $postId
     * @param bool $awaitsMetaBoxSave
     */
    private function queue($postId, $awaitsMetaBoxSave)
    {
        if (empty(self::$queued)) {
            add_action('shutdown', [$this, 'settleQueued']);
        }

        // Saved twice in one request, the post has no second request to wait
        // for once either save said so.
        self::$queued[$postId] = isset(self::$queued[$postId])
            ? self::$queued[$postId] && $awaitsMetaBoxSave
            : $awaitsMetaBoxSave;
    }

    /**
     * Whether a meta-box save follows this publish: the block editor saved the
     * post, and shows classic meta boxes for its type.
     *
     * @param WP_Post $post
     *
     * @return bool
     */
    private function awaitsMetaBoxSave($post)
    {
        if (!$this->isBlockEditorSave()) {
            return false;
        }

        $postTypes = (array) Config::getOption(self::EDITOR_META_BOX_OPTION, []);

        return !empty($postTypes[$post->post_type]);
    }

    /**
     * Mirrors which meta boxes the_block_editor_meta_boxes() hands the editor:
     * titled ones that are not only there for back-compat.
     *
     * @param array $screenMetaBoxes location => priority => id => meta box
     *
     * @return bool
     */
    private function hasEditorMetaBoxes($screenMetaBoxes)
    {
        foreach (['side', 'normal', 'advanced'] as $location) {
            $priorities = isset($screenMetaBoxes[$location]) ? (array) $screenMetaBoxes[$location] : [];

            foreach ($priorities as $metaBoxes) {
                foreach ((array) $metaBoxes as $metaBox) {
                    if (empty($metaBox['title']) || !empty($metaBox['args']['__back_compat_meta_box'])) {
                        continue;
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Whether immediate rules can be shared from a background request instead
     * of holding up this one. That request authenticates with this visitor's
     * login cookie, so without one — WP-Cron, a front-end submission, a REST
     * client on an application password — the post is shared right here.
     *
     * @param int $postId
     *
     * @return bool
     */
    private function canShareInBackground($postId)
    {
        return !wp_doing_cron()
            && \defined('LOGGED_IN_COOKIE')
            && !empty($_COOKIE[LOGGED_IN_COOKIE])
            && current_user_can('edit_post', $postId);
    }

    /**
     * The active accounts a rule shares to — its own picks plus whatever its
     * groups expand to, narrowed by the role permissions Pro enforces.
     *
     * @param array $rule
     * @param int   $postId
     *
     * @return array Account models
     */
    private function ruleAccounts($rule, $postId)
    {
        $accountIds = !empty($rule['accounts']['accountIds']) ? $rule['accounts']['accountIds'] : [];
        $groupAccountIds = [];

        if (!empty($rule['accounts']['groupIds']) && class_exists('BitApps\SocialPro\Model\GroupsAccount')) {
            $groupAccountIds = $this->groupsAccountIds($rule['accounts']['groupIds']);
        }

        $allAccountIds = array_unique([...$accountIds, ...$groupAccountIds]);

        if (empty($allAccountIds)) {
            return [];
        }

        $accounts = Account::whereIn('id', $allAccountIds)
            ->where('status', Account::ACCOUNT_STATUS['active'])
            ->get();

        if (!\is_array($accounts)) {
            return [];
        }

        // Pro drops the accounts the acting user's role isn't permitted on.
        // Free passes the list through.
        $accounts = Hooks::applyFilter(
            Config::withPrefix('autopost_accounts'),
            $accounts,
            $this->actingUserId($postId),
            $postId
        );

        return \is_array($accounts) ? $accounts : [];
    }

    /**
     * The user auto-post publishes on behalf of — mirrors publisherCanAutoPost:
     * the logged-in publisher, or the post author when publishing runs with no
     * logged-in user (WP-Cron for scheduled/delayed posts).
     *
     * @param int $postId
     *
     * @return int
     */
    private function actingUserId($postId)
    {
        if (is_user_logged_in()) {
            return get_current_user_id();
        }

        $post = get_post($postId);

        return $post ? absint($post->post_author) : 0;
    }

    /**
     * Who may trigger auto-post sharing. Scheduled posts are published by
     * WP-Cron with no logged-in user, so the post author is checked instead.
     *
     * Free applies no capability of its own. Auto-post is site-level
     * configuration an administrator turned on, and before this gate existed
     * every published post went out under it — including posts whose author
     * holds no editing rights at all, which is ordinary for front-end
     * submissions, imported content and authors who were later demoted.
     * Gating those on 'edit_posts' would silently stop sharing them.
     *
     * Pro filters the capability to its role-access cap once an admin has
     * configured Roles & Access, so only granted roles trigger auto-post, and
     * further loosens the gate (autopost_publisher_allowed) for publishers whose
     * role is explicitly granted on an account's permissions.
     *
     * @param WP_Post $post
     *
     * @return bool
     */
    private function publisherCanAutoPost($post)
    {
        $userId = is_user_logged_in() ? get_current_user_id() : absint($post->post_author);

        // No acting user at all — WP-CLI, feed importers and other programmatic
        // publishers create posts with post_author 0. There is no role to check,
        // and these posts auto-posted before this gate existed, so let the
        // admin's site-level auto-post configuration stand.
        if (!$userId) {
            return true;
        }

        $capability = (string) Hooks::applyFilter(Config::withPrefix('autopost_publisher_capability'), '');

        $canPublish = $capability === '' || user_can($userId, $capability);

        return (bool) Hooks::applyFilter(Config::withPrefix('autopost_publisher_allowed'), $canPublish, $userId);
    }

    private function sanitizeRequestCookies(): array
    {
        $cookies = [];

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Cookies are only proxied to preserve the current authenticated AJAX session.
        foreach ((array) $_COOKIE as $cookieKey => $cookieValue) {
            if (!\is_scalar($cookieValue)) {
                continue;
            }

            $cookies[sanitize_key((string) $cookieKey)] = sanitize_text_field(wp_unslash((string) $cookieValue));
        }

        return $cookies;
    }
}
