<?php

namespace BitApps\Social\HTTP\Services\AutoPost;

use BitApps\Social\Config;
use WP_Post;

/**
 * Auto-post rules — "which posts go to which accounts, saying what".
 *
 * Auto-post used to be one site-wide configuration: one set of post types, one
 * set of accounts, one delay. It is now a list of rules, each carrying its own
 * post types, taxonomy filter, accounts, delay and post content — the same
 * pieces a schedule carries, which is why the rule editor reuses the schedule
 * modal's tabs.
 *
 * Rules act independently: a post matching two rules is shared once per rule,
 * so two rules naming the same account deliberately post to it twice. Only the
 * master switch and the log setting stay site-wide.
 */
class AutoPostRuleService
{
    public const CONDITION_ALL = 'all';

    public const CONDITION_INCLUDE = 'include';

    public const CONDITION_EXCLUDE = 'exclude';

    /**
     * A rule that is live: it shares posts when it is switched on.
     */
    public const STATUS_PUBLISH = 'publish';

    /**
     * A rule still being put together: it shares nothing until published.
     */
    public const STATUS_DRAFT = 'draft';

    /**
     * The taxonomy list the pre-rules screen offered. It only ever chose which
     * taxonomy pickers that screen showed, so it is answered from here rather
     * than stored — nothing reads it any more.
     */
    public const LEGACY_TAXONOMIES = ['categories', 'tags'];

    /**
     * The release that replaced the single auto-post configuration with rules.
     *
     * Everything below this constant — legacyView(), applyLegacyUpdate(),
     * needsLegacyView() and their callers — exists to carry sites across that
     * one boundary, because Free and Pro update one at a time and a Pro build
     * from before it cannot read the rules payload. Two Pro builds from either
     * side of it can read each other, so a plain 1.20 / 1.19 skew is left
     * alone. Once no supported Pro predates this, the whole shim comes out.
     */
    public const RULES_RELEASE = '1.19.0';

    /**
     * What a freshly created rule looks like.
     *
     * @return array
     */
    public static function defaults()
    {
        return [
            'id'        => '',
            'name'      => __('Auto post', 'bit-social'),
            'status'    => self::STATUS_PUBLISH,
            'isEnabled' => true,
            'postType'  => ['post'],
            'filter'    => [
                'condition' => self::CONDITION_ALL,
                'terms'     => [],
            ],
            'accounts' => [
                'accountIds' => [],
                'groupIds'   => [],
            ],
            'postDelay' => [
                'every' => 0,
                'unit'  => '',
            ],
            'templates' => [],
        ];
    }

    /**
     * The stored settings with their rules normalized.
     *
     * Installs configured before rules existed carry their single auto-post
     * setup in the top-level `accounts`, `postType` and `postDelay` keys; it
     * becomes rule one so they keep posting exactly as they did.
     *
     * @param array $settings
     *
     * @return array
     */
    public static function normalizeSettings($settings)
    {
        if (!\is_array($settings)) {
            $settings = [];
        }

        $rules = isset($settings['rules']) && \is_array($settings['rules']) ? $settings['rules'] : [];

        // An empty list still means "migrate me" while the pre-rules keys are
        // there: reading the settings fills missing keys in from the defaults
        // first, so a legacy install arrives here carrying an empty `rules`
        // alongside the single configuration it was actually set up with.
        if (empty($rules) && self::hasLegacySettings($settings)) {
            $rules = [self::ruleFromLegacySettings($settings)];
        }

        $settings['rules'] = array_values(array_filter(array_map([self::class, 'normalizeRule'], $rules)));

        // The legacy keys are what the rule was built from — keeping them would
        // leave two copies of the same setting drifting apart.
        unset($settings['accounts'], $settings['postType'], $settings['postDelay'], $settings['taxonomies'], $settings['taxonomyFilter']);

        return $settings;
    }

    /**
     * Whether the admin app on screen is a Pro build from before rules.
     *
     * @return bool
     */
    public static function needsLegacyView()
    {
        return self::isPreRulesVersion(Config::proBundleVersion());
    }

    /**
     * @param string $version '' when there is no Pro bundle in play
     *
     * @return bool
     */
    public static function isPreRulesVersion($version)
    {
        return \is_string($version)
            && $version !== ''
            && version_compare($version, self::RULES_RELEASE, '<');
    }

    /**
     * The settings in the single-configuration shape that predates rules.
     *
     * Free and Pro update separately, and with Pro licensed it is Pro that
     * ships the admin app — so between the two updates a Pro build from before
     * rules is reading this endpoint. It reads `postDelay`, `accounts`,
     * `postType` and `taxonomies` straight off the response and destructures
     * them, so serving it the rules shape blanks the Auto Post screen. Rule one
     * is what it was, so rule one is what it gets back.
     *
     * Only rule one, though: that build has nowhere to show a second. A site
     * that already has several sees the rest again once Pro is updated — they
     * are untouched in storage, which stays in the rules shape throughout.
     *
     * @param array $settings already normalized settings
     *
     * @return array
     */
    public static function legacyView($settings)
    {
        $rules = isset($settings['rules']) && \is_array($settings['rules']) ? $settings['rules'] : [];
        $rule = empty($rules) ? self::defaults() : reset($rules);

        return [
            'isEnabled'  => !empty($settings['isEnabled']),
            'keepLogs'   => !isset($settings['keepLogs']) || !empty($settings['keepLogs']),
            'accounts'   => $rule['accounts'],
            'postType'   => $rule['postType'],
            'postDelay'  => $rule['postDelay'],
            'taxonomies' => self::LEGACY_TAXONOMIES,
        ];
    }

    /**
     * Stored settings with a pre-rules save folded into rule one.
     *
     * The other side of legacyView(): the Pro build that was handed rule one is
     * saving it back, in the shape it read. Writing that as-is would drop every
     * other rule the site has, so it edits rule one in place and leaves the
     * rest alone.
     *
     * @param array $stored   the settings as they are on disk
     * @param array $incoming the pre-rules payload that was saved
     *
     * @return array
     */
    public static function applyLegacyUpdate($stored, $incoming)
    {
        $settings = self::normalizeSettings($stored);
        $rules = $settings['rules'];
        $rule = empty($rules) ? self::defaults() : $rules[0];

        foreach (['postType', 'accounts', 'postDelay'] as $key) {
            if (isset($incoming[$key])) {
                $rule[$key] = $incoming[$key];
            }
        }

        if (empty($rules)) {
            $rules[] = $rule;
        } else {
            $rules[0] = $rule;
        }

        $settings['rules'] = $rules;
        $settings['isEnabled'] = !empty($incoming['isEnabled']);
        $settings['keepLogs'] = !empty($incoming['keepLogs']);

        return self::normalizeSettings($settings);
    }

    /**
     * @param mixed $rule
     *
     * @return array|null null when the entry isn't a rule at all
     */
    public static function normalizeRule($rule)
    {
        if (!\is_array($rule)) {
            return;
        }

        $defaults = self::defaults();

        $postTypes = isset($rule['postType']) && \is_array($rule['postType'])
            ? array_values(array_filter(array_map('sanitize_key', $rule['postType'])))
            : [];

        $filter = isset($rule['filter']) && \is_array($rule['filter']) ? $rule['filter'] : [];
        $accounts = isset($rule['accounts']) && \is_array($rule['accounts']) ? $rule['accounts'] : [];
        $delay = isset($rule['postDelay']) && \is_array($rule['postDelay']) ? $rule['postDelay'] : [];

        return [
            'id'   => self::normalizeId(isset($rule['id']) ? $rule['id'] : ''),
            'name' => isset($rule['name']) && \is_scalar($rule['name']) && $rule['name'] !== ''
                ? sanitize_text_field((string) $rule['name'])
                : $defaults['name'],
            'status'    => isset($rule['status']) && $rule['status'] === self::STATUS_DRAFT ? self::STATUS_DRAFT : self::STATUS_PUBLISH,
            'isEnabled' => isset($rule['isEnabled']) ? filter_var($rule['isEnabled'], FILTER_VALIDATE_BOOLEAN) : true,
            'postType'  => empty($postTypes) ? $defaults['postType'] : $postTypes,
            'filter'    => [
                'condition' => self::normalizeCondition(isset($filter['condition']) ? $filter['condition'] : null),
                'terms'     => self::normalizeTerms(isset($filter['terms']) ? $filter['terms'] : null),
            ],
            'accounts' => [
                'accountIds' => self::normalizeTerms(isset($accounts['accountIds']) ? $accounts['accountIds'] : null),
                'groupIds'   => self::normalizeTerms(isset($accounts['groupIds']) ? $accounts['groupIds'] : null),
            ],
            'postDelay' => [
                'every' => isset($delay['every']) ? absint($delay['every']) : 0,
                'unit'  => isset($delay['unit']) && \is_scalar($delay['unit']) ? sanitize_key((string) $delay['unit']) : '',
            ],
            'templates' => isset($rule['templates']) && \is_array($rule['templates']) ? $rule['templates'] : [],
        ];
    }

    /**
     * The rules a post should be shared by, in order.
     *
     * @param array   $settings
     * @param WP_Post $post
     *
     * @return array
     */
    public static function rulesForPost($settings, $post)
    {
        if (empty($settings['rules']) || !\is_array($settings['rules']) || !$post) {
            return [];
        }

        return array_values(array_filter($settings['rules'], function ($rule) use ($post) {
            return self::matchesPost($rule, $post);
        }));
    }

    /**
     * @param array   $rule
     * @param WP_Post $post
     *
     * @return bool
     */
    public static function matchesPost($rule, $post)
    {
        // A draft rule is one still being put together — it shares nothing,
        // whatever else it says.
        if (isset($rule['status']) && $rule['status'] === self::STATUS_DRAFT) {
            return false;
        }

        if (empty($rule['isEnabled']) || !$post) {
            return false;
        }

        if (!\in_array($post->post_type, (array) $rule['postType'], true)) {
            return false;
        }

        return self::matchesFilter($rule['filter'], $post->ID);
    }

    /**
     * Whether a post satisfies a rule's taxonomy filter.
     *
     * @param array $filter
     * @param int   $postId
     *
     * @return bool
     */
    public static function matchesFilter($filter, $postId)
    {
        $condition = isset($filter['condition']) ? $filter['condition'] : self::CONDITION_ALL;
        $terms = isset($filter['terms']) && \is_array($filter['terms']) ? $filter['terms'] : [];

        if ($condition === self::CONDITION_ALL) {
            return true;
        }

        // A condition with nothing selected yet can't narrow anything — treat
        // it as "all posts" rather than silently muting the rule.
        if (empty($terms)) {
            return true;
        }

        $matched = !empty(array_intersect(self::withTranslations($terms), self::postTermIds($postId)));

        return $condition === self::CONDITION_EXCLUDE ? !$matched : $matched;
    }

    /**
     * Whether the rule waits before sharing instead of publishing right away.
     *
     * @param array $rule
     *
     * @return bool
     */
    public static function isDelayed($rule)
    {
        return !empty($rule['postDelay']['every']) && !empty($rule['postDelay']['unit']);
    }

    /**
     * The settings with a deleted account dropped from every rule that named
     * it, so a rule's account list can't outlive the connection it points at.
     *
     * @param array $settings
     * @param int   $accountId
     *
     * @return array
     */
    public static function removeAccount($settings, $accountId)
    {
        $accountId = absint($accountId);

        if (empty($settings['rules']) || !\is_array($settings['rules']) || !$accountId) {
            return $settings;
        }

        foreach ($settings['rules'] as $index => $rule) {
            if (empty($rule['accounts']['accountIds']) || !\is_array($rule['accounts']['accountIds'])) {
                continue;
            }

            $settings['rules'][$index]['accounts']['accountIds'] = array_values(
                array_filter(
                    $rule['accounts']['accountIds'],
                    function ($id) use ($accountId) {
                        return absint($id) !== $accountId;
                    }
                )
            );
        }

        return $settings;
    }

    /**
     * The rule's post content per platform: whatever it carries of its own,
     * with the site's platform templates underneath for anything it doesn't.
     *
     * @param array $rule
     * @param array $templates platform => template
     *
     * @return array
     */
    public static function templatesFor($rule, $templates)
    {
        if (empty($rule['templates']) || !\is_array($rule['templates'])) {
            return $templates;
        }

        foreach ($rule['templates'] as $platform => $template) {
            if (\is_array($template) && !empty($template)) {
                $templates[$platform] = $template;
            }
        }

        return $templates;
    }

    /**
     * A rule id that is safe to use as an array key and stable across saves.
     *
     * @param mixed $id
     *
     * @return string
     */
    private static function normalizeId($id)
    {
        $id = \is_scalar($id) ? preg_replace('/[^A-Za-z0-9_-]/', '', (string) $id) : '';

        return $id === '' ? uniqid('apr_') : $id;
    }

    /**
     * Whether these settings still carry the single auto-post configuration
     * that predates rules — and whether it was ever set up.
     *
     * Accounts are what makes it one: every install stored a post type,
     * defaulted to `post`, so keying on that would hand a rule to sites that
     * never picked an account and never shared anything. Post types and the
     * delay come along once accounts say there is something to carry over.
     *
     * @param array $settings
     *
     * @return bool
     */
    private static function hasLegacySettings($settings)
    {
        return !empty($settings['accounts']['accountIds'])
            || !empty($settings['accounts']['groupIds']);
    }

    /**
     * @param array $settings
     *
     * @return array
     */
    private static function ruleFromLegacySettings($settings)
    {
        $rule = self::defaults();
        $rule['isEnabled'] = true;

        if (isset($settings['postType'])) {
            $rule['postType'] = $settings['postType'];
        }

        if (isset($settings['accounts'])) {
            $rule['accounts'] = $settings['accounts'];
        }

        if (isset($settings['postDelay'])) {
            $rule['postDelay'] = $settings['postDelay'];
        }

        return $rule;
    }

    /**
     * @param mixed $condition
     *
     * @return string
     */
    private static function normalizeCondition($condition)
    {
        $condition = \is_scalar($condition) ? (string) $condition : '';

        return \in_array($condition, [self::CONDITION_INCLUDE, self::CONDITION_EXCLUDE], true)
            ? $condition
            : self::CONDITION_ALL;
    }

    /**
     * @param mixed $ids
     *
     * @return int[]
     */
    private static function normalizeTerms($ids)
    {
        if (!\is_array($ids)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('absint', $ids))));
    }

    /**
     * The selected terms plus their translations, on multilingual sites.
     *
     * WPML and Polylang give every language its own term row, so an Arabic post
     * carries the Arabic "News" term, not the English one the admin picked in
     * the rule. Expanding the selection to its translations means one pick
     * covers the post in every language — matching what the admin means by
     * "posts in News" — instead of silently skipping the translated posts.
     *
     * @param int[] $termIds
     *
     * @return int[]
     */
    private static function withTranslations($termIds)
    {
        $languages = apply_filters('wpml_active_languages', null);
        $hasPolylang = \function_exists('pll_get_term_translations');

        if (!\is_array($languages) && !$hasPolylang) {
            return $termIds;
        }

        $expanded = $termIds;

        foreach ($termIds as $termId) {
            if ($hasPolylang) {
                $expanded = array_merge($expanded, array_values((array) pll_get_term_translations($termId)));
            }

            if (!\is_array($languages)) {
                continue;
            }

            $term = get_term($termId);

            if (!$term || is_wp_error($term)) {
                continue;
            }

            foreach (array_keys($languages) as $language) {
                $translated = apply_filters('wpml_object_id', $termId, $term->taxonomy, false, $language);

                if ($translated) {
                    $expanded[] = $translated;
                }
            }
        }

        return array_values(array_unique(array_map('absint', $expanded)));
    }

    /**
     * Term ids assigned to the post across every taxonomy of its post type.
     *
     * @param int $postId
     *
     * @return int[]
     */
    private static function postTermIds($postId)
    {
        $post = get_post(absint($postId));

        if (!$post) {
            return [];
        }

        $taxonomies = get_object_taxonomies($post->post_type);

        if (empty($taxonomies)) {
            return [];
        }

        $termIds = wp_get_object_terms($post->ID, $taxonomies, ['fields' => 'ids']);

        return is_wp_error($termIds) ? [] : array_map('absint', (array) $termIds);
    }
}
