<?php

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Migration\Migration;

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound -- Migration class loaded dynamically by the migration system; BS prefix denotes Bit Social.
final class BSPluginOptionsUpdate extends Migration
{
    public function up()
    {
        Config::updateOption('db_version', Config::DB_VERSION, true);
        Config::updateOption('version', Config::VERSION, true);
    }

    public function down()
    {
    }
}
