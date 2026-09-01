<?php

namespace BitApps\Social\HTTP\Services\Schedule;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
use BitApps\Social\Deps\BitApps\WPKit\Helpers\Arr;
use BitApps\Social\Deps\BitApps\WPKit\Helpers\DateTimeHelper;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\HTTP\Controllers\ScheduleController;
use BitApps\Social\HTTP\Controllers\WpPostController;
use BitApps\Social\Model\Log;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Utils\Common;
use BitApps\SocialPro\Config as ProConfig;
use BitApps\SocialPro\Model\GroupsAccount;

class ScheduleInfo
{
    use Common;

    public $scheduleId;

    public $config;

    public $accounts;

    public $filters;

    public $templates;

    public $settings;

    public $schedule;

    public $scheduleType;

    public $publishedPostIds = [];

    public $postIdUpdated = false;

    public function __construct($schedule)
    {
        $this->scheduleId = $schedule['schedule_id'];
    }

    public function config()
    {
        $schedule = Schedule::findOne(['id' => $this->scheduleId]);
        if (is_countable($schedule) && \count($schedule) === 0) {
            return false;
        }

        $config = \is_string($schedule['config']) ? json_decode($schedule['config'], true) : $schedule['config'];

        $this->schedule = $schedule;
        $this->config = $config;

        return $this->config;
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

    public function accounts()
    {
        $config = $this->config();
        $accountIds = !empty($config['accounts']['accountIds']) ? $config['accounts']['accountIds'] : [];
        $groupAccountIds = [];

        if (!empty($config['accounts']['groupIds']) && class_exists(ProConfig::class)) {
            $groupAccountIds = $this->groupsAccountIds($config['accounts']['groupIds']);
        }

        $allAccountIds = array_unique([...$accountIds, ...$groupAccountIds]);

        $this->accounts = $allAccountIds;

        return $allAccountIds;
    }

    public function postFilters()
    {
        $config = $this->config();
        $postFilters = !empty($config['post_filters']) ? $config['post_filters'] : [];
        $this->filters = $postFilters;

        return $postFilters;
    }

    public function templates()
    {
        $config = $this->config();
        $templates = !empty($config['templates']) ? $config['templates'] : [];
        $this->templates = $templates;

        return $templates;
    }

    public function postFilterArgument()
    {
        $postFilters = $this->postFilters();
        $publishedPostIds = $this->publishedPostIds();
        $settings = $this->settings();

        $orderTypeLatestToOld = 4;
        $randomly = 2;
        $randomWithoutDuplicate = 1;

        $scheduleOrderRandomly = (int) $settings['post_publish_order'] === $randomly || (int) $settings['post_publish_order'] === $randomWithoutDuplicate;

        $filter['post_type'] = !empty($postFilters['post_type']) ? $postFilters['post_type'] : '';
        $filter['tax_query'] = !empty($postFilters['categories_and_tags']) ? (new WpPostController())->categoryAndTags($postFilters['categories_and_tags']) : ''; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- tax_query is required for category/tag filtering.

        if (!$scheduleOrderRandomly && !empty($settings['post_publish_order'])) {
            if ((int) $settings['post_publish_order'] === $orderTypeLatestToOld) {
                $filter['order'] = 'DESC';
            } else {
                $filter['order'] = 'ASC';
            }
        }

        if ($scheduleOrderRandomly) {
            $filter['orderby'] = 'rand';
        }

        if (!empty($postFilters['custom_date_range'])) {
            $filter['date_query'] = [
                'after'     => $postFilters['custom_date_range'][0],
                'before'    => $postFilters['custom_date_range'][1],
                'inclusive' => true,
            ];
        }

        if ((int) $settings['post_publish_order'] !== $randomly && empty($postFilters['specific_postIds']) && !empty($publishedPostIds)) {
            $filter['exclude'] = $publishedPostIds; // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Uses the VIP-preferred `post__not_in` param for WP_Query.
        }

        if ((int) $settings['post_publish_order'] !== $randomly && !empty($postFilters['specific_postIds'])) {
            $filter['include'] = array_values(array_diff($postFilters['specific_postIds'], $publishedPostIds));
        }

        if ((int) $settings['post_publish_order'] === $randomly && !empty($postFilters['specific_postIds'])) {
            $filter['include'] = $postFilters['specific_postIds'];
        }

        return $filter;
    }

    public function settings()
    {
        $config = $this->config();
        $settings = !empty($config['settings']) ? $config['settings'] : [];
        $this->settings = $settings;

        return $settings;
    }

    /**
     * Detect a "catch-up" execution that fired long after its intended slot.
     *
     * When WP-Cron falls behind (e.g. a low-traffic site with no real server
     * cron, or a gap during a plugin update), every overdue schedule can fire
     * together on the recovery tick and dump a burst of posts at once. If this
     * run is further past its stored next_published_at than the allowed grace
     * (see staleThresholdDefault(), which floors that grace at both a full
     * interval and an hour), we treat it as stale so the caller can log the
     * slot as missed and realign to the next one instead of publishing.
     *
     * next_published_at is stored in WP-local wall-clock, and current_time('mysql')
     * is too, so comparing both via strtotime() is timezone-safe (the assumed
     * timezone cancels out).
     *
     * @return bool
     */
    public function isExecutionStale()
    {
        // settings() loads config(), which populates $this->schedule from the DB.
        $settings = $this->settings();
        if (empty($settings['post_interval_value']) || empty($settings['post_interval_type'])) {
            return false;
        }

        $nextPublishedAt = isset($this->schedule->next_published_at) ? $this->schedule->next_published_at : null;
        if (empty($nextPublishedAt)) {
            return false;
        }

        $intervalSeconds = CustomSchedule::getDelayInSeconds(
            $settings['post_interval_value'],
            $settings['post_interval_type']
        );

        // Grace before a run counts as stale, and filterable so a site can
        // widen/disable it (return <= 0 to disable).
        $threshold = (int) apply_filters(
            Config::VAR_PREFIX . 'stale_execution_threshold',
            $this->staleThresholdDefault($intervalSeconds),
            $this->scheduleId,
            $intervalSeconds
        );
        if ($threshold <= 0) {
            return false;
        }

        $overdueSeconds = strtotime(current_time('mysql')) - strtotime($nextPublishedAt);

        return $overdueSeconds > $threshold;
    }

    /**
     * The slot this run was meant to fill, in WP-local wall-clock.
     *
     * Read before nextPostUpdate() overwrites it: a missed run logs the slot it
     * lost and anchors the next one to it, so a late tick does not drag the
     * whole timetable along with it.
     *
     * @return null|string
     */
    public function intendedSlot()
    {
        if (empty($this->schedule)) {
            $this->config();
        }

        return isset($this->schedule->next_published_at) ? $this->schedule->next_published_at : null;
    }

    public function publishedPostIds():array
    {
        $publishedPostIds = !empty($this->schedule['published_post_ids']) ? explode(',', $this->schedule['published_post_ids']) : [];
        $this->publishedPostIds = $publishedPostIds;

        return $this->publishedPostIds;
    }

    public function orderPosts($posts)
    {
        $publishedPostIds = $this->publishedPostIds();
        $settings = $this->settings();
        $randomly = 2;
        $randomWithoutDuplicate = 1;

        if (!empty($settings['post_publish_order']) && (int) $settings['post_publish_order'] === $randomly) {
            return $posts[array_rand($posts)];
        }

        $posts = array_filter($posts, function ($post) use ($publishedPostIds) {
            return !\in_array($post['id'], $publishedPostIds);
        });

        $this->postIdUpdated = true;

        if (empty($posts)) {
            return false;
        }

        if (!empty($settings['post_publish_order']) && (int) $settings['post_publish_order'] === $randomWithoutDuplicate) {
            return $posts[array_rand($posts)];
        }

        return array_shift($posts);
    }

    public function isNextPost($posts)
    {
    }

    public function postIdUpdate($post)
    {
        if (!empty($post['id'])) {
            $this->publishedPostIds[] = $post['id'];
            $postIds = implode(',', $this->publishedPostIds);

            $this->schedule->update(['published_post_ids' => $postIds]);
            $this->schedule->published_post_ids = $postIds;

            return (bool) ($this->schedule->save());
        }

        return true;
    }

    /**
     * Move the schedule on to its next slot.
     *
     * Normally that is measured from now, which is also when the post went out.
     * A missed run passes the slot it failed to fill as $anchor so the next one
     * is measured from the user's own timetable instead of from whenever WP-Cron
     * happened to catch up — otherwise every late tick drags the schedule
     * further from the times that were picked.
     *
     * If anchoring would land the next slot in the past (cron was down for
     * longer than an interval), fall back to now: one missed slot per outage,
     * rather than one per interval until the schedule catches up.
     *
     * @param null|string $anchor WP-local wall-clock slot, or null for "now"
     *
     * @return bool
     */
    public function nextPostUpdate($anchor = null)
    {
        $scheduleController = new ScheduleController();
        $this->schedule->config = $this->config();
        $dateTimeHelper = new DateTimeHelper();
        $currentTime = $dateTimeHelper->getCurrentDateTime();

        if (!empty($anchor)) {
            $this->schedule->last_published_at = $anchor;
            $anchoredNextPostTime = $scheduleController->getScheduleNextPostTime($this->schedule);

            if (!empty($anchoredNextPostTime) && strtotime($anchoredNextPostTime) > strtotime($currentTime)) {
                $this->schedule->update(['last_published_at' => $anchor, 'next_published_at' => $anchoredNextPostTime]);

                return (bool) ($this->schedule->save());
            }
        }

        $this->schedule->last_published_at = $currentTime;
        $nextPostTime = $scheduleController->getScheduleNextPostTime($this->schedule);
        $this->schedule->update(['last_published_at' => $currentTime, 'next_published_at' => $nextPostTime]);

        return (bool) ($this->schedule->save());
    }

    public function scheduleType()
    {
        $schedule = Schedule::findOne(['id' => $this->scheduleId]);
        if (is_countable($schedule) && \count($schedule) === 0) {
            return false;
        }
        $this->scheduleType = $schedule['schedule_type'];

        return $this->scheduleType;
    }

    /**
     * Retire a schedule that has run out of posts.
     *
     * "Completed" is the right word for a schedule that got through its list —
     * a recurring one holds many posts, so one late slot among them is not the
     * headline. But a schedule that reached the end of that list without ever
     * publishing anything did not complete; every slot it had was missed, and
     * calling that Completed hides the outage behind a green tag. So the
     * terminal state is decided by what actually went out.
     *
     * @param bool $currentRunMissed the run retiring the schedule was itself a
     *                               missed slot — its log rows are written after
     *                               this call, so it cannot be counted from the
     *                               table yet
     *
     * @return bool
     */
    public function scheduleComplete($currentRunMissed = false)
    {
        $this->schedule->cron_status = Schedule::cronStatus['INACTIVE'];
        $this->schedule->status = $this->nothingEverPublished($currentRunMissed)
            ? Schedule::status['MISSED']
            : Schedule::status['COMPLETED'];
        $this->schedule->ended_at = gmdate('Y-m-d H:i:s', current_time('timestamp'));
        $this->schedule->next_published_at = null;

        return (bool) ($this->schedule->save());
    }

    /**
     * A recurring schedule fires on an interval (SCHEDULE_SHARE with an interval,
     * or a repeating DIRECT_SHARE). A non-recurring one runs from a single cron
     * event — so if that run bails without publishing, there is no future event
     * and the schedule would hang in ACTIVE forever. Callers use this to decide
     * whether a bail should be terminal (see scheduleMissed()).
     *
     * @return bool
     */
    public function isRecurring()
    {
        $settings = $this->settings();

        if ((int) $this->scheduleType() === Schedule::scheduleType['DIRECT_SHARE']) {
            return !empty($settings['repeat']);
        }

        return !empty($settings['post_interval_value']) && !empty($settings['post_interval_type']);
    }

    /**
     * Terminal state for a schedule whose slot passed without posting (cron
     * behind, no accounts, source post gone). Mirrors scheduleComplete() but
     * flags it MISSED so the UI can surface it instead of leaving it "In
     * Progress" forever, and clears its cron hook.
     *
     * @return bool
     */
    public function scheduleMissed()
    {
        $this->schedule->cron_status = Schedule::cronStatus['INACTIVE'];
        $this->schedule->status = Schedule::status['MISSED'];
        $this->schedule->ended_at = gmdate('Y-m-d H:i:s', current_time('timestamp'));
        $this->schedule->next_published_at = null;
        $result = (bool) ($this->schedule->save());

        $this->removeScheduleHook($this->scheduleId);

        if ($result) {
            // Same action the reaper fires, always with a list, so listeners
            // handle a single write-off and a whole sweep the same way.
            Hooks::doAction(Config::withPrefix('schedules_missed'), [$this->scheduleId]);
        }

        return $result;
    }

    /**
     * The free plugin only publishes schedules created today. Side-effect free,
     * so callers can test the limit without tripping it — isScheduleCreatedToday()
     * retires the schedule as a side effect and cannot be used to ask a question.
     *
     * @return bool
     */
    public function isCreatedToday():bool
    {
        return gmdate('Y-m-d', strtotime($this->schedule['created_at']))
            === gmdate('Y-m-d', current_time('timestamp'));
    }

    public function isScheduleCreatedToday():bool
    {
        if ($this->isCreatedToday()) {
            return true;
        }

        $this->scheduleComplete();
        $this->removeScheduleHook($this->scheduleId);

        return false;
    }

    public function isAllPostPublished($posts)
    {
        $schedulePostIds = array_column($posts, 'id');
        $publishPostIdsArray = explode(',', $this->schedule->published_post_ids);
        $publishPostIds = array_map('intval', $publishPostIdsArray);

        sort($schedulePostIds);
        sort($publishPostIds);

        return $publishPostIds === $schedulePostIds;
    }

    /**
     * How late a recurring run may be before it stops publishing.
     *
     * One full interval by default: on a site with no real server cron, ticks
     * only land when a visitor does, so a tight window would write off most
     * slots. Pro can trade that forgiveness for punctuality by setting the
     * schedule window in Cron Setup — but only an explicitly saved value counts,
     * so neither the shipped default nor Free's fixed constant silently makes
     * every recurring schedule stricter than it was before upgrading.
     *
     * Zero means "never stale" (publish however late), matching how the same
     * setting reads for one-shot posts.
     *
     * @param int   $intervalSeconds
     * @param mixed $currentRunMissed
     *
     * @return int
     */
    /**
     * Whether this schedule is retiring without a single post having gone out,
     * with at least one slot lost to a late cron.
     *
     * Read off the log rather than a counter on the schedule: the log is where
     * "published" and "missed" are already recorded per account, and it stays
     * right for a schedule that was edited, paused or resumed mid-run.
     *
     * A schedule whose posts were all *rejected* by the platforms is not this
     * case — that is a failure to report, not a slot that never ran — so a
     * missed row has to be present, not merely no successful one.
     *
     * @param bool $currentRunMissed
     *
     * @return bool
     */
    private function nothingEverPublished($currentRunMissed)
    {
        global $wpdb;

        $table = Connection::wpPrefix() . Config::VAR_PREFIX . 'logs';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Identifiers cannot be bound; the values go through prepare().
        $tally = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT SUM(`status` = 1) AS published, SUM(`status` = 0 AND `details` LIKE %s) AS missed
                FROM `{$table}` WHERE `schedule_id` = %d",
                '%"reason":"' . Log::reason['MISSED'] . '"%',
                $this->scheduleId
            ),
            ARRAY_A
        );

        $published = empty($tally['published']) ? 0 : (int) $tally['published'];
        $missed = empty($tally['missed']) ? 0 : (int) $tally['missed'];

        if ($currentRunMissed) {
            ++$missed;
        }

        return $published === 0 && $missed > 0;
    }

    /**
     * How late a recurring run may be and still publish.
     *
     * Two floors, and the window is never below either of them.
     *
     * One interval, because a schedule realigns to `now + interval` every time
     * it runs: on a site whose cron only ticks when a visitor arrives, the gap
     * between ticks is whatever the traffic is, and a grace of less than that
     * gap makes *every* run stale. The schedule then never publishes again -
     * silently, for as long as the site stays quiet - where before this guard
     * existed it published late. A quarter-hourly schedule on an hourly tick is
     * exactly that case, and it is a common shape of site.
     *
     * An hour, for the same reason the one-shot reaper allows one (see
     * CustomSchedule::DEFAULT_AUTOPOST_WINDOW): an update must not turn a site
     * that has always been a little behind into a site that has stopped posting.
     *
     * The Pro setting exists to widen this - up to "publish however late" at 0 -
     * so a stored value raises the window and cannot lower it past the floors.
     * Otherwise saving Cron Setup at its defaults would quietly cut a daily
     * schedule's grace from a day to an hour, which is not what that screen
     * claims to do. A site that really does want a tighter window has the
     * `stale_execution_threshold` filter, which is applied to what this returns.
     *
     * @param int $intervalSeconds
     *
     * @return int seconds, 0 meaning "never stale"
     */
    private function staleThresholdDefault($intervalSeconds)
    {
        $floor = max(
            (int) $intervalSeconds,
            CustomSchedule::windowSeconds(CustomSchedule::DEFAULT_SCHEDULE_WINDOW)
        );

        if (!Config::isProActivated()) {
            return $floor;
        }

        $saved = Config::getOption('settings', []);
        $stored = isset($saved['missedSchedule']) ? (array) $saved['missedSchedule'] : [];

        if (!isset($stored['schedule']) && !isset($stored['scheduleHours'])) {
            return $floor;
        }

        $configured = CustomSchedule::windowSeconds(
            CustomSchedule::readWindow($stored, 'schedule', CustomSchedule::DEFAULT_SCHEDULE_WINDOW)
        );

        return $configured <= 0 ? 0 : max($configured, $floor);
    }
}
