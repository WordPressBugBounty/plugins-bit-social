<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Services\Schedule\CustomSchedule;
use BitApps\Social\Model\Account;
use BitApps\Social\Model\Log;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Utils\Creator;
use BitApps\Social\Utils\LogRunBackfill;

final class LogController
{
    /**
     * @var null|bool
     */
    private static $hasRunId;

    /**
     * @var null|bool
     */
    private static $hasUnstamped;

    /**
     * Resolved creators, keyed by user id, for the life of one request.
     *
     * @var array<int, array{name: null|string, role: null|string}>
     */
    private $creators = [];

    /**
     * The logs screen, one line per publish run rather than one per account.
     *
     * A schedule with ten accounts writes ten rows every time it fires, which
     * reads as ten separate events when it is really one. Rows are grouped by
     * run_id and each group reports its own tally — "8 published, 2 failed" —
     * with the rows themselves nested underneath for the expanded view.
     *
     * Filters select *runs*, not rows: asking for failures shows the runs that
     * had one, still counting everything that happened in them. Filtering the
     * rows instead would report "2 failed" on a run that also published 8.
     *
     * @param int $page
     * @param int $limit
     */
    public function index(Request $request, $page, $limit)
    {
        $page = max(1, (int) $page);
        $limit = max(1, (int) $limit);
        $offset = ($page - 1) * $limit;

        $table = self::table();
        $groupKey = self::groupKey();

        $scope = $this->runScope($request);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Identifiers cannot be bound; every value goes through Connection::prepare below.
        $countSql = "SELECT COUNT(DISTINCT {$groupKey}) FROM `{$table}` {$scope['sql']}";

        if (!empty($scope['bindings'])) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared right here.
            $countSql = Connection::prepare($countSql, $scope['bindings']);
        }

        $totalRuns = $this->countRuns($countSql);

        $data = $this->attachRuns($this->runTotals($this->pageRunIds($scope, $limit, $offset)));

        $pages = $limit > 0 ? (int) ceil($totalRuns / $limit) : 0;

        return Response::success([
            'data'          => $data,
            'pages'         => $pages,
            'total'         => $totalRuns,
            'current_total' => \count($data),
            'current_page'  => $page,
            'last_page'     => $pages,
            'per_page'      => $limit,
        ]);
    }

    /**
     * The rows of one run, for the expanded view and for retrying a whole run.
     *
     * @param string $runId
     */
    public function showRun(Request $request, $runId)
    {
        $logs = $this->runLogs([$runId]);

        if (empty($logs)) {
            return Response::error('Run not found');
        }

        return Response::success($this->decorate($logs));
    }

    /**
     * Republish every account of a run that did not succeed.
     *
     * A missed slot writes one row per account, so retrying them one at a time
     * means ten clicks for one lost post. Successful rows are left alone — the
     * point is to fill the gaps, not to post twice.
     *
     * @param string $runId
     */
    public function retryRun(Request $request, $runId)
    {
        $logs = $this->runLogs([$runId]);

        if (empty($logs)) {
            return Response::error('Run not found');
        }

        $retryController = new RetryController();
        $attempted = 0;
        $failed = 0;

        foreach ($logs as $log) {
            if ((int) $log['status'] === Log::status['SUCCESS']) {
                continue;
            }

            ++$attempted;

            $result = $retryController->publishFromLog(
                $log['schedule_id'],
                (array) $log['details'],
                $log['id']
            );

            if (empty($result)) {
                ++$failed;
            }
        }

        if ($attempted === 0) {
            return Response::error('Nothing to retry — every account in this run already published.');
        }

        if ($failed === $attempted) {
            return Response::error('Retry failed for every account. Check the accounts still exist and the post is still published.');
        }

        return Response::success(
            \sprintf('Retried %1$d of %2$d accounts. Check the log for per-account results.', $attempted - $failed, $attempted)
        );
    }

    public function destroy(Request $request)
    {
        Log::whereIn('id', $request->logIds)->delete();

        self::flushRunCounts();

        return Response::success('Selected logs deleted');
    }

    /**
     * Total runs behind the current filter.
     *
     * Counting them is a second pass over the whole table on top of the page
     * query — nothing in this plugin prunes the log, so on an install that has
     * been running for years that is the expensive half of opening the screen,
     * and pagination pays it again for every page. A minute-old count only moves
     * the last-page marker, and the one change a user makes by hand — deleting
     * rows — drops the cache outright.
     *
     * @param string $sql
     * @param mixed  $scope
     * @param mixed  $limit
     * @param mixed  $offset
     *
     * @return int
     */
    /**
     * The run ids on this page, newest first.
     *
     * Deliberately asks for nothing but the key and one number. The tally a run
     * line shows has to look at `details` (a missed slot is a marker inside it),
     * and reading that column for every row in the table just to throw all but
     * one page away is what makes the logs screen slow on an install that has
     * been logging for years — nothing in this plugin prunes that table. So the
     * page is chosen first, over an index, and only its rows are totalled.
     *
     * Ordered by the largest row id in each run rather than the largest
     * timestamp: same order, since ids are handed out in insertion order and
     * created_at records the same moment, but MySQL can read an id straight out
     * of the index it is already grouping by.
     *
     * @param array{sql: string, bindings: array} $scope
     * @param int                                 $limit
     * @param int                                 $offset
     *
     * @return array
     */
    private function pageRunIds($scope, $limit, $offset)
    {
        $table = self::table();
        $groupKey = self::groupKey();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Identifiers cannot be bound; every value goes through prepare().
        $rows = Connection::get_results(
            Connection::prepare(
                "SELECT {$groupKey} AS group_id, MAX(`id`) AS anchor
                FROM `{$table}` {$scope['sql']}
                GROUP BY group_id
                ORDER BY anchor DESC
                LIMIT %d OFFSET %d",
                array_merge($scope['bindings'], [$limit, $offset])
            ),
            ARRAY_A
        );

        return \is_array($rows) ? array_column($rows, 'group_id') : [];
    }

    /**
     * What each of those runs adds up to.
     *
     * Bounded to the page's runs, so the LIKE that separates a missed slot from
     * a refused post is evaluated over one screenful of rows rather than the
     * whole table.
     *
     * @param array $runIds in the order the page wants them
     *
     * @return array
     */
    private function runTotals($runIds)
    {
        $runIds = array_values(array_filter($runIds, 'strlen'));

        if (empty($runIds)) {
            return [];
        }

        $table = self::table();
        $groupKey = self::groupKey();
        $missedLike = self::missedLike();
        $placeholders = implode(',', array_fill(0, \count($runIds), '%s'));

        // Without the column there is nothing to take the max of; the group key
        // stands in, so retrying a run still addresses the same rows.
        $runIdSelect = self::hasRunIdColumn() ? 'MAX(`run_id`)' : $groupKey;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Identifiers cannot be bound; every value goes through prepare().
        $rows = Connection::get_results(
            Connection::prepare(
                "SELECT {$groupKey} AS group_id,
                    {$runIdSelect} AS run_id,
                    MAX(`schedule_id`) AS schedule_id,
                    MAX(`created_at`) AS created_at,
                    COUNT(*) AS total,
                    SUM(`status` = 1) AS success_count,
                    SUM(`status` = 0 AND `details` LIKE %s) AS missed_count,
                    SUM(`status` = 0 AND `details` NOT LIKE %s) AS failed_count
                FROM `{$table}`
                WHERE {$groupKey} IN ({$placeholders})
                GROUP BY group_id",
                array_merge([$missedLike, $missedLike], $runIds)
            ),
            ARRAY_A
        );

        if (!\is_array($rows)) {
            return [];
        }

        // GROUP BY makes no promise about order, and the order was already
        // decided by the query that picked the page.
        $byId = array_column($rows, null, 'group_id');
        $ordered = [];

        foreach ($runIds as $runId) {
            if (isset($byId[$runId])) {
                $ordered[] = $byId[$runId];
            }
        }

        return $ordered;
    }

    private function countRuns($sql)
    {
        $key = Config::VAR_PREFIX . 'log_runs_' . self::countsVersion() . '_' . md5($sql);
        $cached = get_transient($key);

        if ($cached !== false) {
            return (int) $cached;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Prepared by the caller.
        $total = (int) Connection::get_var($sql);

        set_transient($key, $total, MINUTE_IN_SECONDS);

        return $total;
    }

    /**
     * Namespaces the cached counts so deleting rows retires every one of them at
     * once, whatever filter produced it — the keys are hashes of their own SQL
     * and cannot be enumerated.
     *
     * @return int
     */
    private static function countsVersion()
    {
        return (int) Config::getOption('log_counts_version', 0);
    }

    private static function flushRunCounts()
    {
        Config::updateOption('log_counts_version', self::countsVersion() + 1);
    }

    /**
     * Whether the run_id column is there yet.
     *
     * Migrations run on a plugin version bump, so an install that is already on
     * this version — a tester on an earlier build of it, an upgrade that failed
     * part way — can be running this code against a table without the column.
     * The logs screen going blank would be a worse bug than the one grouping
     * fixes, so it is checked rather than assumed.
     *
     * @return bool
     */
    private static function hasRunIdColumn()
    {
        if (self::$hasRunId === null) {
            $table = self::table();

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound; built from the plugin's own prefix.
            self::$hasRunId = !empty(Connection::get_var("SHOW COLUMNS FROM `{$table}` LIKE 'run_id'"));
        }

        return self::$hasRunId;
    }

    /**
     * What makes two rows the same run.
     *
     * Normally the stamped run id, with anything still null treated as a run of
     * one so it never merges with an unrelated row. Without the column, the same
     * approximation the migration backfills with: schedule plus timestamp, which
     * groups correctly except for a schedule that published twice in one second.
     *
     * @return string
     */
    private static function groupKey()
    {
        if (!self::hasRunIdColumn()) {
            return "MD5(CONCAT(COALESCE(`schedule_id`, 0), '-', `created_at`))";
        }

        // With no null left, the column on its own is the key — and the column
        // is indexed, which an expression around it can never be: MySQL cannot
        // group over an index it has to compute a value from. On a table nothing
        // prunes, that is the difference between a page that loads and one that
        // times out.
        //
        // Otherwise the fallback has to tell nulls apart, or every unstamped row
        // on the site would collapse into one enormous run.
        return self::hasUnstampedRows()
            ? "COALESCE(`run_id`, CONCAT('log-', `id`))"
            : '`run_id`';
    }

    /**
     * Whether any row is still missing its run id.
     *
     * Asked of the table rather than inferred from the backfill's state. The
     * backfill only answers for the rows that existed when it ran, and it is not
     * the only writer: a handful of places log a standalone event with a direct
     * insert instead of going through LoggerTrait, and a build that forgets the
     * column adds another. Reading "the backfill finished" as "there are no
     * nulls" is how those rows would end up grouped together as one run of
     * everything — a wrong logs screen, to save a query that the index answers
     * without touching a row (`ref` on run_id_index, `Using index`).
     *
     * @return bool
     */
    private static function hasUnstampedRows()
    {
        if (self::$hasUnstamped !== null) {
            return self::$hasUnstamped;
        }

        // Before the backfill finishes there are certainly nulls, and saying so
        // costs nothing.
        if (!LogRunBackfill::isComplete()) {
            return self::$hasUnstamped = true;
        }

        $table = self::table();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound; built from the plugin's own prefix.
        self::$hasUnstamped = (bool) Connection::get_var("SELECT 1 FROM `{$table}` WHERE `run_id` IS NULL LIMIT 1");

        if (self::$hasUnstamped) {
            // Reading the slow key is the safe answer, not a resting state: left
            // alone this install would read it for the rest of its life, because
            // the only thing that stamps a row had already declared itself
            // finished. Ask it to come back for the stragglers, and the next
            // screen after it does gets the indexed key.
            LogRunBackfill::reopen();
        }

        return self::$hasUnstamped;
    }

    private static function table()
    {
        return Config::withDBPrefix('logs');
    }

    /**
     * LIKE pattern matching the missed marker inside the encoded details.
     *
     * @return string
     */
    private static function missedLike()
    {
        return '%"reason":"' . Log::reason['MISSED'] . '"%';
    }

    /**
     * One filter off the request, as a string.
     *
     * Absent means unfiltered, which every caller here spells 'all' — so that
     * is what a missing filter reads as, and no caller has to remember that the
     * property may not be there.
     *
     * @param string $name
     * @param string $absent value to stand in for a filter that was not sent
     *
     * @return string
     */
    private static function filterValue(Request $request, $name, $absent = 'all')
    {
        $value = isset($request->{$name}) ? $request->{$name} : null;

        if ($value === null || \is_array($value) || \is_object($value)) {
            return $absent;
        }

        return (string) $value;
    }

    /**
     * Turns the screen's filters into "runs having at least one row like this".
     *
     * @return array{sql: string, bindings: array}
     */
    private function runScope(Request $request)
    {
        $table = self::table();
        $groupKey = self::groupKey();

        $conditions = [];
        $bindings = [];

        // The screen leaves a filter off the request entirely when it is not set
        // (see activeFilters), so every one of these can be absent. Read them
        // through one accessor that answers 'all' for a filter that is not
        // there: reaching for the raw property means handing null to a string
        // parameter, which on PHP 8.1+ is a deprecation on every request — and
        // the logs screen polls, so it fills debug.log by itself.
        $search = trim((string) self::filterValue($request, 'search', ''));

        if ($search !== '') {
            $conditions[] = self::searchCondition();
            // One term, matched against four places; the condition names them
            // in this order.
            $bindings = array_merge($bindings, array_fill(0, 4, '%' . Connection::esc_like($search) . '%'));
        }

        $date = self::filterValue($request, 'date');

        if ($date !== 'all' && ($dateRange = json_decode(stripslashes($date)))) {
            $conditions[] = 'DATE(`created_at`) BETWEEN %s AND %s';
            $bindings[] = $dateRange[0];
            $bindings[] = $dateRange[1];
        }

        $schedule = self::filterValue($request, 'schedule');

        if ($schedule !== 'all') {
            $conditions[] = '`schedule_id` = %d';
            $bindings[] = (int) $schedule;
        }

        $platform = self::filterValue($request, 'platform');

        if ($platform !== 'all' && $platform !== '') {
            $conditions[] = '`platform` = %s';
            $bindings[] = $platform;
        }

        $sourceFilter = self::filterValue($request, 'source');

        if ($sourceFilter !== 'all') {
            $source = $this->sourceCondition($sourceFilter);

            if (!empty($source)) {
                $conditions[] = $source['sql'];
                $bindings = array_merge($bindings, $source['bindings']);
            }
        }

        $status = self::filterValue($request, 'status');

        if ($status !== 'all') {
            // "Missed" is not a status value — the column is a boolean and a
            // missed slot is stored as FAIL with a marker in details. Splitting
            // them here is what lets "Error" mean "a platform refused it",
            // which is the distinction worth filtering on.
            if ($status === Log::reason['MISSED']) {
                $conditions[] = '(`status` = 0 AND `details` LIKE %s)';
                $bindings[] = self::missedLike();
            } elseif ((int) $status === Log::status['FAIL']) {
                $conditions[] = '(`status` = 0 AND `details` NOT LIKE %s)';
                $bindings[] = self::missedLike();
            } else {
                $conditions[] = '`status` = %d';
                $bindings[] = Log::status['SUCCESS'];
            }
        }

        if (empty($conditions)) {
            return ['sql' => '', 'bindings' => []];
        }

        $sql = "WHERE {$groupKey} IN (SELECT {$groupKey} FROM `{$table}` WHERE "
            . implode(' AND ', $conditions) . ')';

        return ['sql' => $sql, 'bindings' => $bindings];
    }

    /**
     * Where a search term is looked for.
     *
     * The four things the screen actually shows: the account and any message
     * the platform returned (both live in the stored details), the platform
     * itself, the schedule's name, and the post's title. A run matches when any
     * one of its rows does — searching an account name finds the whole run it
     * published in, which is the run you wanted to look at.
     *
     * @return string
     */
    private static function searchCondition()
    {
        $schedules = Config::withDBPrefix('schedules');
        $posts = Connection::prop('posts');

        // Same extraction the auto-post backfill uses: the post id out of the
        // encoded details, without asking for JSON functions the plugin's
        // supported MySQL versions may not have.
        $postId = "TRIM(BOTH '\"' FROM TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, '\"post_id\":', -1), ',', 1), '}', 1)))";

        return "(`details` LIKE %s
            OR `platform` LIKE %s
            OR `schedule_id` IN (SELECT `id` FROM `{$schedules}` WHERE `name` LIKE %s)
            OR {$postId} IN (SELECT `ID` FROM `{$posts}` WHERE `post_title` LIKE %s))";
    }

    /**
     * Restricts runs to one of the three sources.
     *
     * Source is not a column on the log — it comes from the schedule behind the
     * row — so this reaches into the schedules table rather than duplicating the
     * answer onto every log row, which would go stale the moment a schedule was
     * edited.
     *
     * @param string $source
     *
     * @return null|array{sql: string, bindings: array}
     */
    private function sourceCondition($source)
    {
        $schedules = Config::withDBPrefix('schedules');

        // Both ways a row can be marked as auto-post generated: the flag newer
        // rows carry, and the name older ones are only identifiable by.
        $autoPostMatch = '(`config` LIKE %s OR `name` LIKE %s)';
        $autoPostBindings = ['%"source":"' . CustomSchedule::AUTO_POST_SOURCE . '"%', CustomSchedule::AUTO_POST_NAME_PREFIX . '%'];

        if ($source === Log::source['AUTO_POST']) {
            return [
                // An auto-post with no delay never creates a schedule at all.
                'sql'      => "(`schedule_id` IS NULL OR `schedule_id` IN (SELECT `id` FROM `{$schedules}` WHERE {$autoPostMatch}))",
                'bindings' => $autoPostBindings,
            ];
        }

        if ($source === Log::source['SHARE_NOW']) {
            return [
                'sql'      => "`schedule_id` IN (SELECT `id` FROM `{$schedules}` WHERE `schedule_type` = %d)",
                'bindings' => [Schedule::scheduleType['DIRECT_SHARE']],
            ];
        }

        if ($source === Log::source['SCHEDULE']) {
            return [
                'sql'      => "`schedule_id` IN (SELECT `id` FROM `{$schedules}` WHERE `schedule_type` = %d AND NOT {$autoPostMatch})",
                'bindings' => array_merge([Schedule::scheduleType['SCHEDULE_SHARE']], $autoPostBindings),
            ];
        }
    }

    /**
     * Loads every row belonging to the given runs in one query — the expanded
     * view needs them all, and fetching per group would be an N+1.
     *
     * @param array $runIds
     *
     * @return array
     */
    private function runLogs($runIds)
    {
        $runIds = array_values(array_filter($runIds, 'strlen'));

        if (empty($runIds)) {
            return [];
        }

        $table = self::table();
        $groupKey = self::groupKey();
        $placeholders = implode(',', array_fill(0, \count($runIds), '%s'));

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Identifiers cannot be bound; the run ids go through prepare().
        $rows = Connection::get_results(
            Connection::prepare(
                "SELECT *, {$groupKey} AS group_id FROM `{$table}` WHERE {$groupKey} IN ({$placeholders}) ORDER BY `id` ASC",
                $runIds
            ),
            ARRAY_A
        );

        if (!\is_array($rows)) {
            return [];
        }

        foreach ($rows as &$row) {
            // Read straight from the connection, so the model's cast has not run.
            $row['details'] = json_decode($row['details'], true);
        }
        unset($row);

        $this->attachAccountIcons($rows);

        return $rows;
    }

    /**
     * Nests each run's rows under its group and labels the group with the
     * schedule and post it belongs to.
     *
     * @param array $groups
     *
     * @return array
     */
    private function attachRuns($groups)
    {
        if (empty($groups)) {
            return [];
        }

        $logs = $this->runLogs(array_column($groups, 'group_id'));
        $schedules = $this->scheduleMeta(array_column($groups, 'schedule_id'));

        $byGroup = [];
        foreach ($logs as $log) {
            $byGroup[$log['group_id']][] = $log;
        }

        $data = [];

        foreach ($groups as $group) {
            $groupId = $group['group_id'];
            $rows = isset($byGroup[$groupId]) ? $byGroup[$groupId] : [];
            $first = !empty($rows) ? $rows[0] : [];
            $postId = isset($first['details']['post_id']) ? $first['details']['post_id'] : null;
            $scheduleId = $group['schedule_id'];

            $meta = isset($schedules[$scheduleId]) ? $schedules[$scheduleId] : null;
            $creator = $this->creatorOf($meta, $postId);

            $data[] = [
                'group_id'        => $groupId,
                'run_id'          => $group['run_id'],
                'schedule_id'     => $scheduleId,
                'schedule_name'   => $meta ? $meta['name'] : null,
                'source'          => $meta ? $meta['source'] : $this->sourceWithoutSchedule($scheduleId),
                'created_by_name' => $creator['name'],
                'created_by_role' => $creator['role'],
                'created_at'      => $group['created_at'],
                'post_id'         => $postId,
                'post_title'      => $postId ? get_the_title($postId) : null,
                'platforms'       => array_values(array_unique(array_column($rows, 'platform'))),
                'total'           => (int) $group['total'],
                'success_count'   => (int) $group['success_count'],
                'failed_count'    => (int) $group['failed_count'],
                'missed_count'    => (int) $group['missed_count'],
                'logs'            => $rows,
            ];
        }

        return $data;
    }

    /**
     * Name and source for each schedule behind the page's runs.
     *
     * @param array $scheduleIds
     *
     * @return array<int, array{name: string, source: string, created_by: int}>
     */
    private function scheduleMeta($scheduleIds)
    {
        $scheduleIds = array_values(array_unique(array_filter($scheduleIds)));

        if (empty($scheduleIds)) {
            return [];
        }

        $schedules = Schedule::whereIn('id', $scheduleIds)->get(['id', 'name', 'schedule_type', 'config']);

        $meta = [];

        if (is_countable($schedules)) {
            foreach ($schedules as $schedule) {
                $meta[$schedule['id']] = [
                    'name'       => $schedule['name'],
                    'source'     => $this->sourceOf($schedule),
                    'created_by' => Creator::idFromConfig($schedule['config']),
                ];
            }
        }

        return $meta;
    }

    /**
     * Which of the three things produced this run.
     *
     * A delayed auto-post is stored as an ordinary one-post schedule, so the
     * schedule type alone cannot tell it apart from a campaign the user built —
     * CustomSchedule::isAutoPostSchedule() is the same check the missed-post
     * reaper uses to decide how much lateness each kind is worth.
     *
     * @param array $schedule
     *
     * @return string
     */
    private function sourceOf($schedule)
    {
        $config = \is_string($schedule['config']) ? json_decode($schedule['config'], true) : (array) $schedule['config'];
        $settings = isset($config['settings']) ? $config['settings'] : [];

        if (CustomSchedule::isAutoPostSchedule($schedule, $settings)) {
            return Log::source['AUTO_POST'];
        }

        if ((int) $schedule['schedule_type'] === Schedule::scheduleType['DIRECT_SHARE']) {
            return Log::source['SHARE_NOW'];
        }

        return Log::source['SCHEDULE'];
    }

    /**
     * A run with no schedule row behind it.
     *
     * An auto-post with no delay publishes on the spot and never creates a
     * schedule, so its rows carry no schedule_id — that absence is the only
     * thing identifying them. A row whose schedule was deleted keeps its id and
     * is reported as unknown rather than guessed at.
     *
     * @param null|int $scheduleId
     *
     * @return null|string
     */
    private function sourceWithoutSchedule($scheduleId)
    {
        return empty($scheduleId) ? Log::source['AUTO_POST'] : null;
    }

    /**
     * @param array $logs
     *
     * @return array
     */
    private function decorate($logs)
    {
        return array_map(static function ($log) {
            unset($log['group_id']);

            return $log;
        }, $logs);
    }

    /**
     * Attaches each row's account avatar, batched into one query for the whole
     * page so the expanded run does not do an N+1 lookup per account.
     *
     * Rows written before avatars were stored, and accounts since disconnected,
     * resolve to null — the client falls back to the account's initial, so an
     * old log still lines up with a new one.
     *
     * @param array $logs
     */
    private function attachAccountIcons(array &$logs)
    {
        $accountIds = [];

        foreach ($logs as $log) {
            if (!empty($log['details']['account_id'])) {
                $accountIds[] = (string) $log['details']['account_id'];
            }
        }

        $iconMap = [];

        if (!empty($accountIds)) {
            $accounts = Account::whereIn('account_id', array_values(array_unique($accountIds)))
                ->get(['account_id', 'details']);

            foreach ((array) $accounts as $account) {
                $iconMap[(string) $account->account_id] = isset($account->details->icon) ? $account->details->icon : null;
            }
        }

        foreach ($logs as &$log) {
            $accountId = !empty($log['details']['account_id']) ? (string) $log['details']['account_id'] : '';
            $log['account_icon'] = isset($iconMap[$accountId]) ? $iconMap[$accountId] : null;
        }
        unset($log);
    }

    /**
     * Who is answerable for a run.
     *
     * Schedules and Share Now record the user on their config; an auto-post has
     * no schedule to record it on, so the published post's author is the only
     * attribution there is. Anything older than the config field — or a user
     * since deleted — resolves to nulls, which the client shows as "Unknown"
     * rather than leaving the column blank.
     *
     * @param null|array      $meta
     * @param null|int|string $postId
     *
     * @return array{name: null|string, role: null|string}
     */
    private function creatorOf($meta, $postId)
    {
        $userId = ($meta && !empty($meta['created_by'])) ? (int) $meta['created_by'] : 0;

        if (!$userId && $postId) {
            $userId = (int) get_post_field('post_author', absint($postId));
        }

        if (!$userId) {
            return ['name' => null, 'role' => null];
        }

        // One page shows a handful of creators over many runs; resolving each
        // one once keeps get_userdata() off the per-row path.
        if (!isset($this->creators[$userId])) {
            $this->creators[$userId] = Creator::info($userId);
        }

        return $this->creators[$userId];
    }
}
