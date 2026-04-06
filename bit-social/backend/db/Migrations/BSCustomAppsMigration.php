<?php

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPDatabase\Blueprint;
use BitApps\Social\Deps\BitApps\WPDatabase\Connection;
use BitApps\Social\Deps\BitApps\WPDatabase\Schema;
use BitApps\Social\Deps\BitApps\WPKit\Migration\Migration;

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound -- Migration class loaded dynamically by the migration system; BS prefix denotes Bit Social.
final class BSCustomAppsMigration extends Migration
{
    public function up()
    {
        Schema::withPrefix(Connection::wpPrefix() . Config::VAR_PREFIX)->create('custom_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform');
            $table->longtext('credential');
            $table->boolean('status')->defaultValue(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::withPrefix(Connection::wpPrefix() . Config::VAR_PREFIX)->drop('custom_apps');
    }
}
