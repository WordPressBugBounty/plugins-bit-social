<?php

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Blueprint;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
use BitApps\Social\Deps\BitApps\WPDatabase\Schema;
use BitApps\Social\Deps\BitApps\WPKit\Migration\Migration;
use BitApps\Social\Utils\LogRunBackfill;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adds `run_id` to the logs table.
 *
 * One publish run writes one row per account, so a schedule with ten accounts
 * used to fill ten lines of the log with no way to tell they belonged together.
 * A run id stamped on every row of a run lets the screen collapse them to a
 * single line and report "8 published, 2 failed".
 *
 * Only the column is added here. Filling it in for rows written before it
 * existed is cosmetic, unbounded, and would run inside the first request after
 * updating — so it is handed to LogRunBackfill, which does it in the background
 * in small batches. A row it has not reached yet reads as a run of one, exactly
 * as it did before this column existed.
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound -- Migration class loaded dynamically by the migration system; BS prefix denotes Bit Social.
final class BSLogsRunIdMigration extends Migration
{
    public function up()
    {
        $table = Config::withDBPrefix('logs');

        // Migrations re-run on every version bump, and Blueprint's edit() emits
        // a bare ADD COLUMN — so ask the table itself whether the column is
        // already there. A stored db_version would answer the wrong question on
        // an install where the ALTER failed but the version still moved on.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound as a parameter; it is built from the plugin's own prefix.
        $exists = Connection::get_var("SHOW COLUMNS FROM `{$table}` LIKE 'run_id'");

        if (empty($exists)) {
            try {
                Schema::withPrefix(Config::get('WP_DB_PREFIX') . Config::VAR_PREFIX)
                    ->edit('logs', function (Blueprint $column) {
                        $column->string('run_id')
                            ->length(32)
                            ->nullable()
                            ->defaultValue('NULL')
                            ->index();
                    });
            } catch (Exception $e) {
                // Blueprint throws on any SQL error and the migration runner
                // calls up() without a catch of its own, so an unhandled throw
                // here would fatal wp-admin and skip every migration queued
                // behind this one. The column staying absent is survivable:
                // the log screen falls back to grouping by schedule and time.
            }
        }

        LogRunBackfill::start();
    }

    public function down()
    {
    }
}
