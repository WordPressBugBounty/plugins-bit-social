<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Helpers\Arr;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
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

    public $defaultAutoPostSettings = [
        'isEnabled'  => false,
        'keepLogs'   => true,
        'taxonomies' => ['categories', 'tags'],
        'accounts'   => [
            'accountIds' => [],
            'groupIds'   => []
        ],
        'postType'  => ['post'],
        'postDelay' => [
            'every' => 0,
            'unit'  => ''
        ]
    ];

    public function autoPostSettings()
    {
        return Response::success($this->getAutoPostSettings());
    }

    public function update(Request $request)
    {
        Config::updateOption('auto_post_settings', $request->autoPostSettings);

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

        return $autoPostSettings;
    }

    public function publishWpPost($postId, $post, $update, $postBefore)
    {
        $autoPostSettings = $this->getAutoPostSettings();

        $postDelay = $autoPostSettings['postDelay'];

        $delayTypeImmediately = $postDelay['every'] === 0 && $postDelay['unit'] === '';

        if ('publish' !== $post->post_status || !\in_array($post->post_type, $autoPostSettings['postType']) || ($postBefore && 'publish' === $postBefore->post_status)) {
            return;
        }

        if (!$autoPostSettings['isEnabled']) {
            return;
        }

        if (!$this->publisherCanAutoPost($post)) {
            return;
        }

        if (!$delayTypeImmediately && class_exists('BitApps\SocialPro\HTTP\Services\AutoPost\AutoPostService')) {
            return AutoPostService::createAutoPostDelay($post, $postId, $autoPostSettings);
        }

        if ((!isset($postBefore->post_status) && $post->post_status === 'publish') || ($post->post_status === 'publish' && $postBefore->post_status === 'future')) {
            return $this->executeSocialPost($postId);
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

    public function executeSocialPost($postId = null)
    {
        $isAjaxRequest = wp_doing_ajax();

        if ($isAjaxRequest) {
            check_ajax_referer(Config::withPrefix('nonce'));

            if (!current_user_can('edit_posts')) {
                wp_send_json_error(['message' => 'Insufficient permission.'], 403);
            }
        }

        if (null === $postId && $isAjaxRequest) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is validated at the start of this method.
            $postId = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;
        }

        $postId = absint($postId);

        if (0 === $postId) {
            if ($isAjaxRequest) {
                wp_send_json_error(['message' => 'Invalid post ID.'], 400);
            }

            return;
        }

        if ($isAjaxRequest && !current_user_can('edit_post', $postId)) {
            if ($isAjaxRequest) {
                wp_send_json_error(['message' => 'You are not allowed to access this post.'], 403);
            }

            return;
        }

        if (!get_post($postId)) {
            if ($isAjaxRequest) {
                wp_send_json_error(['message' => 'Post not found.'], 404);
            }

            return;
        }

        $templates = (array) (new SocialTemplateController())->getSocialTemplates();

        $autoPostSettings = $this->getAutoPostSettings();

        $accountIds = !empty($autoPostSettings['accounts']['accountIds']) ? $autoPostSettings['accounts']['accountIds'] : [];
        $groupAccountIds = [];

        if (!empty($autoPostSettings['accounts']['groupIds']) && class_exists('BitApps\SocialPro\Model\GroupsAccount')) {
            $groupAccountIds = $this->groupsAccountIds($autoPostSettings['accounts']['groupIds']);
        }

        $allAccountIds = array_unique([...$accountIds, ...$groupAccountIds]);
        $accounts = [];

        if (!empty($allAccountIds)) {
            $accounts = Account::whereIn('id', $allAccountIds)
                ->where('status', Account::ACCOUNT_STATUS['active'])
                ->get();
        }

        if (!\is_array($accounts)) {
            return;
        }

        // Pro filters out accounts the acting user's role isn't permitted on
        // (per-account permissions). Free passes the list through unchanged.
        $accounts = Hooks::applyFilter(
            Config::withPrefix('autopost_accounts'),
            $accounts,
            $this->actingUserId($postId)
        );

        if (!\is_array($accounts)) {
            return;
        }

        $isSleep = false;
        $publishPostData = [];

        // One auto-post is one run. Without this every account logs under an id
        // of its own — LogRun::current() mints one per write when nothing is
        // open — and the Logs screen shows a five-account auto-post as five
        // separate events. A scheduled run gets this from SocialExecution;
        // auto-post publishes here instead and had no equivalent.
        LogRun::start();

        try {
            foreach ($accounts as $account) {
                $isPlatFormExists = $this->isPlatFormExists($account->id);

                if (!$isPlatFormExists) {
                    continue;
                }

                $platform = new Social(new $isPlatFormExists['class']());

                $platformName = $isPlatFormExists['platform'];

                if (isset($templates[$platformName])) {
                    $template = $templates[$platformName];

                    preg_match('/custom_field_\[([^\]]+)\]/', $template['content'] ?? '', $matches);

                    if (!$isSleep && isset($matches[1])) {
                        $isSleep = true;
                        sleep(2); // Wait for the post meta values to be saved after the hook call
                    }

                    $data = [
                        'post'            => (array) get_post($postId),
                        'template'        => $template,
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

        if ($isAjaxRequest) {
            wp_send_json_success($publishPostData);
        }
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
