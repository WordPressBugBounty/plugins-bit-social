<?php

namespace BitApps\Social\Providers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Installer;
use BitApps\Social\Model\Schedule;

class InstallerProvider
{
    private $_activateHook;

    private $_deactivateHook;

    private static $_uninstallHook;

    public function __construct()
    {
        register_activation_hook(Config::get('MAIN_FILE'), [$this, 'registerActivator']);
        register_deactivation_hook(Config::get('MAIN_FILE'), [$this, 'registerDeactivator']);
        $this->_activateHook = Config::withPrefix('activate');
        $this->_deactivateHook = Config::withPrefix('deactivate');
        self::$_uninstallHook = Config::withPrefix('uninstall');

        Hooks::addAction($this->_deactivateHook, [$this, 'deactivate']);

        // Only a static class method or function can be used in an uninstall hook.
        register_uninstall_hook(Config::get('MAIN_FILE'), [self::class, 'registerUninstaller']);
    }

    public function register()
    {
        $installer = new Installer(
            [
                'php'        => Config::REQUIRED_PHP_VERSION,
                'wp'         => Config::REQUIRED_WP_VERSION,
                'version'    => Config::VERSION,
                'oldVersion' => Config::getOption('version', '0.0'),
                'multisite'  => true,
                'basename'   => Config::get('BASENAME'),
            ],
            [
                'activate'  => $this->_activateHook,
                'uninstall' => self::$_uninstallHook,
            ],
            [

                'migration' => $this->migration(),
                'drop'      => $this->drop(),
            ]
        );
        $installer->register();
    }

    public function deactivate($networkWide)
    {
        $schedules = Schedule::get(['id']);
        if ($schedules && is_countable($schedules) && \count($schedules) > 0) {
            foreach ($schedules as $schedule) {
                $hook = Config::VAR_PREFIX . $schedule['id'] . '_cron_exec';
                wp_clear_scheduled_hook($hook, [$schedule->id]);
            }
        }
    }

    public function registerActivator($networkWide)
    {
        Hooks::doAction($this->_activateHook, $networkWide);
    }

    public function registerDeactivator($networkWide)
    {
        Hooks::doAction($this->_deactivateHook, $networkWide);
    }

    public static function registerUninstaller($networkWide)
    {
        Hooks::doAction(self::$_uninstallHook, $networkWide);
    }

    public static function migration()
    {
        $migrations = [
            'BSCustomAppsMigration',
            'BSAccountsMigration',
            'BSFreeGroupTableDrop',
            'BSSchedulesMigration',
            'BSLogsMigration',
            'BSSettingsMigration',
            'BSPluginOptions',
            'BSPluginOptionsUpdate',
        ];

        return [
            'path' => Config::get('BASEDIR')
                . DIRECTORY_SEPARATOR
                . 'db'
                . DIRECTORY_SEPARATOR
                . 'Migrations'
                . DIRECTORY_SEPARATOR,
            'migrations' => $migrations,
        ];
    }

    public static function drop()
    {
        $migrations = [
            'BSSettingsMigration',
            'BSLogsMigration',
            'BSSchedulesMigration',
            'BSAccountsMigration',
            'BSCustomAppsMigration',
            'BSPluginOptions',
        ];

        return [
            'path' => Config::get('BASEDIR')
                . DIRECTORY_SEPARATOR
                . 'db'
                . DIRECTORY_SEPARATOR
                . 'Migrations'
                . DIRECTORY_SEPARATOR,
            'migrations' => $migrations,
        ];
    }
}
