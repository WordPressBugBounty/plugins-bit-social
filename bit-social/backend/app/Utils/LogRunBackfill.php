<?php

namespace BitApps\Social\Utils;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;

/**
 * Fills in `run_id` on log rows written before the column existed.
 *
 * This used to happen inside the migrations themselves, which meant an install
 * with a large logs table — and nothing in this plugin ever prunes that table —
 * ran a whole-table UPDATE and read every auto-post row into PHP during the
 * first request after updating. On a big enough table that request runs out of
 * memory or execution time, and because the migration never finishes, the
 * db_version is never advanced and the *next* request tries the same thing:
 * wp-admin stops loading at all until the table is small enough.
 *
 * So the work moved out of the upgrade path. The migration only adds the column
 * (see BSLogsRunIdMigration) and hands the backfill to a WP-Cron job that
 * chews through it in small batches, stopping the moment it is over budget and
 * booking itself in again for the rest.
 *
 * The backfill is cosmetic — it only decides whether historical rows collapse
 * into one line on the Logs screen. A row it never reaches keeps a null run_id
 * and reads as a run of one, exactly as it did before the column existed
 * (LogController::groupKey coalesces it). Nothing here may risk a request to
 * make old history prettier, which is why every step is bounded and every
 * bound is checked before it is used.
 */
final class LogRunBackfill
{
    /**
     * Cron hook that carries the work.
     */
    public const HOOK = 'bit_social_backfill_log_runs';

    /**
     * Where the sweep left off: ['phase' => run_ids|auto_post|done].
     */
    public const STATE_OPTION = 'log_run_backfill';

    /**
     * How far apart two rows of the same auto-post can be and still be one run.
     * A run's rows are seconds apart (one network call per account, sometimes a
     * sleep between them); the same post auto-posted again later is a genuinely
     * separate event.
     */
    private const RUN_GAP_SECONDS = 120;

    /**
     * Rows per UPDATE. Small enough that the statement cannot hold a lock long
     * enough to be felt by a publish happening at the same time.
     */
    private const BATCH_SIZE = 500;

    /**
     * Wall-clock budget for one cron pass. WP-Cron shares its request with
     * whatever else is due, so this stays well under a default max_execution_time.
     */
    private const TIME_BUDGET = 10;

    /**
     * Ceiling on the auto-post pass, which is the only step that holds rows in
     * memory. Twenty thousand four-column rows is a few MB; the safety check
     * below lowers it further when the process has less headroom than that.
     */
    private const MAX_AUTOPOST_ROWS = 20000;

    /**
     * How far back the auto-post pass looks, in log ids. Its WHERE cannot use an
     * index (the post id lives inside the encoded details), so without a bound
     * it is a full table scan. Recent history is what anyone actually opens.
     */
    private const AUTOPOST_ID_WINDOW = 200000;

    /**
     * Listens for the cron event. Registered on every request — the job has to
     * be reachable from wp-cron.php, which is not an admin request.
     */
    public static function register()
    {
        Hooks::addAction(self::HOOK, [self::class, 'run']);
    }

    /**
     * Queue the backfill (called by the migration that adds the column).
     *
     * Idempotent: re-running the migration on the next version bump finds the
     * state already 'done' and books nothing.
     */
    public static function start()
    {
        $state = self::state();

        if ($state['phase'] === 'done') {
            return;
        }

        self::schedule();
    }

    /**
     * Whether every historical row has been stamped.
     *
     * The Logs screen asks because it decides how runs are grouped: once there
     * is no null left, `run_id` on its own is the key, and that one is indexed.
     * While rows are still unstamped it has to fall back to an expression that
     * tells nulls apart, which no index can serve.
     *
     * @return bool
     */
    public static function isComplete()
    {
        $state = self::state();

        return $state['phase'] === 'done';
    }

    /**
     * Put the sweep back to work after it had already finished.
     *
     * "Finished" only ever meant "finished with the rows that existed at the
     * time". A row written without a run id afterwards - a build that forgets
     * the column, a restore that brings older rows back - leaves the table in a
     * state the sweep is the only thing that can clear, and nothing was asking
     * it to. The Logs screen then falls back to the key it can read safely, an
     * expression no index can serve, and stays there for good.
     *
     * Called by whoever notices a straggler. Idempotent, and self-limiting: the
     * state is no longer 'done' after this, so the caller's own check short-
     * circuits and nobody asks again until the sweep has finished properly.
     */
    public static function reopen()
    {
        if (!self::isComplete()) {
            return;
        }

        self::saveState(['phase' => 'run_ids']);
        self::schedule();
    }

    /**
     * One bounded pass. Picks up wherever the last one stopped and re-books
     * itself while there is anything left to do.
     */
    public static function run()
    {
        $state = self::state();

        if ($state['phase'] === 'done') {
            return;
        }

        if (!self::isSafeToRun()) {
            // Nothing to fix by retrying in a minute — the column is missing or
            // the table is gone, so the rows this exists for do not exist either.
            self::finish();

            return;
        }

        // A pass that overran its budget is re-booked a minute out, which is
        // shorter than a pass can take on a slow database. Without this the two
        // would overlap and do each other's work twice.
        $lock = Config::VAR_PREFIX . 'log_run_backfill_lock';

        if (get_transient($lock)) {
            return;
        }

        set_transient($lock, 1, 5 * MINUTE_IN_SECONDS);

        try {
            $deadline = time() + self::TIME_BUDGET;

            if ($state['phase'] === 'run_ids' && self::backfillRunIds($deadline)) {
                $state['phase'] = 'auto_post';
                self::saveState($state);
            }

            if ($state['phase'] === 'auto_post' && time() < $deadline) {
                self::groupAutoPostRuns();
                self::finish();

                return;
            }
        } finally {
            delete_transient($lock);
        }

        self::schedule();
    }

    /**
     * Whether this install is in a state where the sweep can do its job at all.
     *
     * Checked before every pass rather than once at the start: the job outlives
     * the request that booked it, and by the time it runs the plugin may have
     * been rolled back to a version without the column, or the tables dropped
     * by an uninstall that left the cron event behind.
     *
     * @return bool
     */
    public static function isSafeToRun()
    {
        $table = self::table();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound; built from the plugin's own prefix.
        if (Connection::get_var(Connection::prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
            return false;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- As above.
        return !empty(Connection::get_var("SHOW COLUMNS FROM `{$table}` LIKE 'run_id'"));
    }

    /**
     * Phase one: give every row still missing a run id the best grouping its own
     * data supports — schedule plus timestamp, which is right except for a
     * schedule that published twice within the same second.
     *
     * @param int $deadline unix time this pass must stop by
     *
     * @return bool true when there is nothing left to stamp
     */
    private static function backfillRunIds($deadline)
    {
        $table = self::table();

        do {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Table name cannot be bound; the batch size is an int constant.
            $updated = (int) Connection::query(
                "UPDATE `{$table}`
                SET `run_id` = MD5(CONCAT(COALESCE(`schedule_id`, 0), '-', `created_at`))
                WHERE `run_id` IS NULL
                LIMIT " . self::BATCH_SIZE
            );

            if ($updated < self::BATCH_SIZE) {
                return true;
            }
        } while (time() < $deadline);

        return false;
    }

    /**
     * Phase two: tie the rows of one auto-post back together.
     *
     * An auto-post publishes on the spot rather than through a schedule, and the
     * code that did it never opened a log run — so every account it posted to
     * got a run id of its own and the Logs screen showed one auto-post as five
     * separate events. AutoPostController opens a run now; this is for the rows
     * written before it did.
     *
     * Bounded twice over (see MAX_AUTOPOST_ROWS and AUTOPOST_ID_WINDOW) and run
     * once. Older rows keep the ids phase one gave them, which is what the Logs
     * screen showed for them before this existed.
     */
    private static function groupAutoPostRuns()
    {
        foreach (self::autoPostGroups(self::autoPostRows()) as $group) {
            self::merge($group);
        }
    }

    /**
     * The newest schedule-less rows, with the post id pulled out of the encoded
     * details.
     *
     * Read in SQL rather than by decoding each payload in PHP: the details of a
     * failed post can be a page of platform JSON, and only these four small
     * columns are needed.
     *
     * @return array
     */
    private static function autoPostRows()
    {
        $table = self::table();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound; built from the plugin's own prefix.
        $maxId = (int) Connection::get_var("SELECT MAX(`id`) FROM `{$table}`");
        $floor = max(0, $maxId - self::AUTOPOST_ID_WINDOW);

        $postId = "TRIM(BOTH '\"' FROM TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, '\"post_id\":', -1), ',', 1), '}', 1)))";

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Identifiers and a fixed expression; every value goes through prepare().
        $rows = Connection::get_results(
            Connection::prepare(
                "SELECT `id`, `run_id`, `created_at`, {$postId} AS post_key
                FROM `{$table}`
                WHERE `schedule_id` IS NULL AND `details` LIKE %s AND `id` >= %d
                ORDER BY `id` DESC
                LIMIT %d",
                '%"post_id":%',
                $floor,
                self::rowCap()
            ),
            ARRAY_A
        );

        if (!\is_array($rows)) {
            return [];
        }

        // Ordered by id for the query's sake (that is the index it can walk);
        // grouping needs them by post and then by time.
        usort($rows, static function ($a, $b) {
            return [$a['post_key'], $a['created_at'], (int) $a['id']]
                <=> [$b['post_key'], $b['created_at'], (int) $b['id']];
        });

        return $rows;
    }

    /**
     * How many rows this process can afford to hold.
     *
     * The cap is a guess about a machine we cannot see, so it is checked against
     * the headroom actually available rather than trusted. Roughly 200 bytes a
     * row with PHP's array overhead, and never more than a quarter of what is
     * left — the caller is a cron request that has other jobs to run after this.
     *
     * @return int
     */
    private static function rowCap()
    {
        $limit = self::memoryLimitBytes();

        if ($limit <= 0) {
            return self::MAX_AUTOPOST_ROWS;
        }

        $headroom = $limit - memory_get_usage(true);

        if ($headroom <= 0) {
            return 0;
        }

        return (int) max(0, min(self::MAX_AUTOPOST_ROWS, floor($headroom / 4 / 200)));
    }

    /**
     * php.ini's memory_limit in bytes, or 0 when it is unlimited/unreadable.
     *
     * @return int
     */
    private static function memoryLimitBytes()
    {
        $raw = trim((string) \ini_get('memory_limit'));

        if ($raw === '' || $raw === '-1') {
            return 0;
        }

        $unit = strtolower(substr($raw, -1));
        $value = (int) $raw;

        if ($unit === 'g') {
            return $value * GB_IN_BYTES;
        }

        if ($unit === 'm') {
            return $value * MB_IN_BYTES;
        }

        if ($unit === 'k') {
            return $value * KB_IN_BYTES;
        }

        return $value;
    }

    /**
     * Chains the sorted rows into runs: same post, and no more than
     * RUN_GAP_SECONDS since the row before it.
     *
     * @param array $rows
     *
     * @return array<int, array>
     */
    private static function autoPostGroups($rows)
    {
        $groups = [];
        $index = -1;
        $lastKey = null;
        $lastStamp = 0;

        foreach ($rows as $row) {
            $postKey = trim((string) $row['post_key']);

            // A row whose details carry no usable post id cannot be tied to
            // anything, so it is left as the run of one it already is.
            if ((int) $postKey <= 0) {
                continue;
            }

            $stamp = strtotime($row['created_at']);

            $continues = $index >= 0
                && $lastKey === $postKey
                && $stamp - $lastStamp <= self::RUN_GAP_SECONDS;

            if (!$continues) {
                $groups[] = [];
                ++$index;
            }

            $groups[$index][] = $row;
            $lastKey = $postKey;
            $lastStamp = $stamp;
        }

        return $groups;
    }

    /**
     * Stamps one group's rows with a single id, derived from the group's first
     * row so a repeat pass lands on the same answer instead of churning the
     * column.
     *
     * @param array $rows
     */
    private static function merge($rows)
    {
        if (\count($rows) < 2) {
            return;
        }

        // Already one run — rows written since AutoPostController started
        // opening one. Rewriting them would only churn the column.
        if (\count(array_unique(array_column($rows, 'run_id'))) === 1) {
            return;
        }

        $ids = array_map('intval', array_column($rows, 'id'));
        $runId = md5('bit_social_auto_post_run_' . $ids[0]);
        $placeholders = implode(',', array_fill(0, \count($ids), '%d'));
        $table = self::table();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Table name cannot be bound; every value goes through prepare().
        Connection::query(
            Connection::prepare(
                "UPDATE `{$table}` SET `run_id` = %s WHERE `id` IN ({$placeholders})",
                array_merge([$runId], $ids)
            )
        );
    }

    private static function schedule()
    {
        if (!wp_next_scheduled(self::HOOK)) {
            wp_schedule_single_event(time() + MINUTE_IN_SECONDS, self::HOOK);
        }
    }

    private static function finish()
    {
        self::saveState(['phase' => 'done']);
    }

    /**
     * @return array{phase: string}
     */
    private static function state()
    {
        $state = Config::getOption(self::STATE_OPTION, []);

        if (!\is_array($state) || empty($state['phase'])) {
            return ['phase' => 'run_ids'];
        }

        return $state;
    }

    /**
     * @param array $state
     */
    private static function saveState($state)
    {
        Config::updateOption(self::STATE_OPTION, $state);
    }

    /**
     * @return string
     */
    private static function table()
    {
        return Config::withDBPrefix('logs');
    }
}
