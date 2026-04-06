<?php

use BitApps\Social\Dotenv;

if (! defined('ABSPATH')) {
    exit;
}

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    add_action('admin_notices', static function () {
        $message = sprintf(
            // translators: %s: composer command
            __('Vendor dependencies are missing. Please run %s in the plugin directory.', 'bit-social'),
            '<code>composer install</code>'
        );

        echo '<div class="notice notice-error"><p><strong>'
            . esc_html__('Bit Social:', 'bit-social')
            . '</strong> '
            . wp_kses($message, ['code' => []])
            . '</p></div>';
    });

    return;
}

// Autoload vendor files.
require_once __DIR__ . '/../vendor/autoload.php';

Dotenv::load(plugin_dir_path(__DIR__) . '.env');

// Initialize the plugin.
BitApps\Social\Plugin::load();
