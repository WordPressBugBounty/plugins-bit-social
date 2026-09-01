<?php

namespace BitApps\Social\Utils;

use BitApps\Social\Model\Account;
use BitApps\SocialPro\Model\Group;
use BitApps\SocialPro\Model\GroupsAccount;

/**
 * Turns the ids a schedule / share-now stores in its config into the names,
 * icons and labels the Schedules and Share Now lists show inline.
 *
 * Without this the lists only carry ids, so the one way to see where a row
 * posts and what it posts is to open its edit modal. The lookups are cached for
 * the request: a page of rows resolves every account and group in two queries
 * rather than two per row.
 */
class ScheduleSummary
{
    /**
     * How many of a schedule's hand-picked posts are named before the rest are
     * left as a count — a schedule may pin hundreds, and the panel is a summary.
     */
    private const SPECIFIC_POST_LIMIT = 8;

    /**
     * Every connected account keyed by id, or null before the first lookup.
     *
     * @var null|array<int, array>
     */
    private static $accounts;

    /**
     * Every account group keyed by id, or null before the first lookup. Empty
     * on free, where groups do not exist.
     *
     * @var null|array<int, array>
     */
    private static $groups;

    /**
     * Stamps accounts_detail / groups_detail — and, for a schedule, the
     * post_filter_detail — on a row so they serialize to the frontend list.
     *
     * @param mixed $schedule
     *
     * @return mixed the same row
     */
    public static function attachToSchedule($schedule)
    {
        $config = self::configOf($schedule);
        $accountsConfig = isset($config['accounts']) ? (array) $config['accounts'] : [];

        $groups = self::groupsDetail($accountsConfig);

        $schedule->groups_detail = $groups;
        $schedule->accounts_detail = self::accountsDetail($accountsConfig, $groups);

        // Share Now has no post filters — it carries its own content — so the
        // key is left off its rows rather than sent as an empty shape.
        if (!empty($config['post_filters'])) {
            $schedule->post_filter_detail = self::postFilterDetail((array) $config['post_filters']);
        }

        return $schedule;
    }

    /**
     * @param mixed $schedule
     *
     * @return array
     */
    private static function configOf($schedule)
    {
        $config = $schedule->config;

        return \is_string($config) ? (array) json_decode($config, true) : (array) $config;
    }

    /**
     * The accounts a row posts to: the ones picked by hand plus the members of
     * every group it picked, in one list, because that is what publishing does
     * with them.
     *
     * @param array $accountsConfig {accountIds: int[], groupIds: int[]}
     * @param array $groups         resolved groups of the same row
     *
     * @return array<int, array>
     */
    private static function accountsDetail($accountsConfig, $groups)
    {
        $accounts = self::accounts();

        $ids = !empty($accountsConfig['accountIds']) ? array_map('intval', (array) $accountsConfig['accountIds']) : [];

        // Which group an account arrived through, so the panel can say so
        // instead of showing a name that is nowhere in the account picker.
        $viaGroup = [];

        foreach ($groups as $group) {
            foreach ($group['accountIds'] as $accountId) {
                if (!isset($viaGroup[$accountId])) {
                    $viaGroup[$accountId] = $group['name'];
                }

                $ids[] = $accountId;
            }
        }

        $detail = [];

        foreach (array_unique($ids) as $id) {
            if (!isset($accounts[$id])) {
                continue;
            }

            $account = $accounts[$id];
            $account['group'] = isset($viaGroup[$id]) ? $viaGroup[$id] : null;

            $detail[] = $account;
        }

        return $detail;
    }

    /**
     * @param array $accountsConfig {accountIds: int[], groupIds: int[]}
     *
     * @return array<int, array>
     */
    private static function groupsDetail($accountsConfig)
    {
        if (empty($accountsConfig['groupIds'])) {
            return [];
        }

        $groups = self::groups();
        $detail = [];

        foreach ((array) $accountsConfig['groupIds'] as $groupId) {
            $groupId = (int) $groupId;

            if (isset($groups[$groupId])) {
                $detail[] = $groups[$groupId];
            }
        }

        return $detail;
    }

    /**
     * @return array<int, array>
     */
    private static function accounts()
    {
        if (self::$accounts !== null) {
            return self::$accounts;
        }

        self::$accounts = [];

        $accounts = Account::select(['id', 'account_name', 'details', 'platform', 'status'])->get();

        foreach (self::rows($accounts) as $account) {
            $details = $account->details;
            $details = \is_string($details) ? (array) json_decode($details, true) : (array) $details;

            $id = (int) $account->id;

            self::$accounts[$id] = [
                'icon'     => isset($details['icon']) ? $details['icon'] : '',
                'id'       => $id,
                'name'     => !empty($details['account_name']) ? $details['account_name'] : $account->account_name,
                'platform' => $account->platform,
                'status'   => (int) $account->status,
                'type'     => isset($details['account_type']) ? $details['account_type'] : '',
            ];
        }

        return self::$accounts;
    }

    /**
     * @return array<int, array>
     */
    private static function groups()
    {
        if (self::$groups !== null) {
            return self::$groups;
        }

        self::$groups = [];

        if (!class_exists(Group::class) || !class_exists(GroupsAccount::class)) {
            return self::$groups;
        }

        foreach (self::rows(Group::select(['id', 'name'])->get()) as $group) {
            self::$groups[(int) $group->id] = [
                'accountIds' => [],
                'id'         => (int) $group->id,
                'name'       => $group->name,
            ];
        }

        foreach (self::rows(GroupsAccount::select(['group_id', 'account_id'])->get()) as $link) {
            $groupId = (int) $link->group_id;

            if (isset(self::$groups[$groupId])) {
                self::$groups[$groupId]['accountIds'][] = (int) $link->account_id;
            }
        }

        return self::$groups;
    }

    /**
     * What a schedule pulls its posts from, as labels rather than ids.
     *
     * @param array $filters
     *
     * @return array
     */
    private static function postFilterDetail($filters)
    {
        $detail = [
            'post_type'            => null,
            'specific_posts'       => [],
            'specific_posts_total' => 0,
            'terms'                => [],
        ];

        if (!empty($filters['post_type'])) {
            $postType = $filters['post_type'];
            $postTypeObject = get_post_type_object($postType);

            $detail['post_type'] = [
                'label' => $postTypeObject ? $postTypeObject->labels->name : $postType,
                'name'  => $postType,
            ];
        }

        if (!empty($filters['categories_and_tags'])) {
            foreach ((array) $filters['categories_and_tags'] as $termId) {
                $term = get_term((int) $termId);

                if (!$term || is_wp_error($term)) {
                    continue;
                }

                $taxonomy = get_taxonomy($term->taxonomy);

                $detail['terms'][] = [
                    'id'       => (int) $term->term_id,
                    'name'     => $term->name,
                    'taxonomy' => $taxonomy ? $taxonomy->labels->singular_name : $term->taxonomy,
                ];
            }
        }

        if (!empty($filters['specific_postIds'])) {
            $postIds = array_map('intval', (array) $filters['specific_postIds']);

            $detail['specific_posts_total'] = \count($postIds);

            // One query for the names shown; the rest stay a count.
            $posts = get_posts([
                'post__in'            => \array_slice($postIds, 0, self::SPECIFIC_POST_LIMIT),
                'post_status'         => 'any',
                'post_type'           => 'any',
                'posts_per_page'      => self::SPECIFIC_POST_LIMIT,
                'suppress_filters'    => true,
                'ignore_sticky_posts' => true,
            ]);

            foreach ($posts as $post) {
                $detail['specific_posts'][] = [
                    'id'    => (int) $post->ID,
                    'title' => $post->post_title !== '' ? $post->post_title : __('(no title)', 'bit-social'),
                ];
            }
        }

        return $detail;
    }

    /**
     * The query builder hands back a bare model for a single row and `false`
     * for none, so every caller needs the result as a list.
     *
     * @param mixed $result
     *
     * @return array
     */
    private static function rows($result)
    {
        if (empty($result)) {
            return [];
        }

        return \is_array($result) ? $result : [$result];
    }
}
