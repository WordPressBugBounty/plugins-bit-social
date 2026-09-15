<?php

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Migration\Migration;
use BitApps\Social\HTTP\Services\AutoPost\AutoPostRuleService;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Turns the single auto-post configuration older installs were set up with
 * into auto-post rule one, at update time.
 *
 * Reading the settings migrates them too, so this only makes the timing
 * predictable: an install that updates keeps auto-posting from the moment the
 * new version loads, without waiting for someone to open the Auto Post page.
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound -- Migration class loaded dynamically by the migration system; BS prefix denotes Bit Social.
final class BSAutoPostRulesMigration extends Migration
{
    public function up()
    {
        $settings = Config::getOption('auto_post_settings');

        if (!is_array($settings)) {
            return;
        }

        $normalized = AutoPostRuleService::normalizeSettings($settings);

        if ($normalized !== $settings) {
            Config::updateOption('auto_post_settings', $normalized);
        }
    }

    public function down()
    {
    }
}
