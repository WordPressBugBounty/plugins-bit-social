<?php

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
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
        global $wpdb;

        $table = Connection::wpPrefix() . Config::VAR_PREFIX . 'logs';

        // Migrations re-run on every version bump, and ALTER TABLE ADD COLUMN
        // is not idempotent — ask first.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name cannot be bound as a parameter; it is built from the plugin's own prefix.
        $exists = $wpdb->get_var("SHOW COLUMNS FROM `{$table}` LIKE 'run_id'");

        if (empty($exists)) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- As above; no user input is interpolated.
            $wpdb->query("ALTER TABLE `{$table}` ADD COLUMN `run_id` VARCHAR(32) NULL DEFAULT NULL, ADD INDEX `run_id_index` (`run_id`)");
        }

        LogRunBackfill::start();
    }

    public function down()
    {
    }
}
