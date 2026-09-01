<?php

namespace BitApps\Social;

/*
 * Main class for the plugin.
 *
 * @since 1.0.0-alpha
 */

use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\RequestType;
use BitApps\Social\Deps\BitApps\WPKit\Migration\MigrationHelper;
use BitApps\Social\HTTP\Middleware\NonceCheckerMiddleware;
use BitApps\Social\HTTP\Services\Schedule\CustomSchedule;
use BitApps\Social\Model\Schedule;
use BitApps\Social\Providers\HookProvider;
use BitApps\Social\Providers\InstallerProvider;
use BitApps\Social\Utils\LogRunBackfill;
use BitApps\Social\Views\Layout;
use BitApps\Social\Views\PluginPageActions;
use BitApps\SocialPro\Config as ProConfig;

final class Plugin
{
    public const MAX_FREE_TELEMETRY_VERSION = '1.13.7';

    /**
     * When the schedule maintenance sweep runs on plugins_loaded.
     *
     * After Pro, which boots at 11 and only there makes its licence readable:
     * BitApps\SocialPro\Plugin::setPluginUtilsConfig() configures the option
     * prefix LicenseService reads the licence through, so until it has run,
     * Config::isProActivated() answers false however valid the licence is.
     *
     * The sweep asks that question — it is how CustomSchedule::missedThresholdFor()
     * decides whether the site's own Late post handling windows apply. Running it
     * at 10 meant they never did, in both directions: a five-minute window was
     * ignored and late posts went out on the shipped one-hour default, and
     * "publish however late" was ignored too and posts were written off as Missed
     * after an hour.
     *
     * Still early enough to do its work. wp-cron.php only reads the ready hooks
     * once the whole load is done, so writing off a slot or re-queueing an event
     * here still lands in the run that triggered it.
     */
    public const SCHEDULE_MAINTENANCE_PRIORITY = 12;
    /**
     * Initialize the Plugin with hooks.
     */

    /**
     * Main instance of the plugin.
     *
     * @since 1.0.0-alpha
     *
     * @var null|Plugin
     */
    private static $_instance;

    private $_registeredMiddleware = [];

    public function __construct()
    {
        $this->registerInstaller();

        Hooks::addAction('plugins_loaded', [$this, 'loaded']);
        Hooks::addAction('plugins_loaded', [$this, 'customScheduleInit'], self::SCHEDULE_MAINTENANCE_PRIORITY);
    }

    public function registerInstaller()
    {
        $installerProvider = new InstallerProvider();
        $installerProvider->register();
    }

    /**
     * Load the plugin.
     */
    public function loaded()
    {
        Hooks::doAction(Config::withPrefix('loaded'));
        Hooks::addAction('init', [$this, 'registerProviders'], 11);
        Hooks::addFilter('plugin_action_links_' . Config::get('BASENAME'), [new PluginPageActions(), 'renderActionLinks']);
        $this->recordCronHeartbeat();
        $this->cleanupLegacyTrackingOption();
        LogRunBackfill::register();
        $this->maybeMigrateDB();
    }

    /**
     * Record when WP-Cron last ran on this site — any cron request, not only Bit
     * Social events — so the Cron Setup screen shows a true site-wide "last cron
     * run". plugins_loaded fires during wp-cron.php with DOING_CRON already set.
     */
    public function recordCronHeartbeat()
    {
        if (self::isCronRequest()) {
            Config::updateOption('last_cron_run', time());
        }
    }

    public function customScheduleInit()
    {
        $schedules = Schedule::get(['id', 'name', 'config', 'status', 'started_at', 'next_published_at', 'cron_status', 'schedule_type']);
        if ($schedules && is_countable($schedules) && \count($schedules) > 0) {
            $schedule = new CustomSchedule($schedules);

            if (self::shouldRunScheduleMaintenance()) {
                // Order matters: write off what is too old first, then space out
                // whatever is left so a catch-up drains gradually.
                $schedule->reapMissedSingleEvents();
                $schedule->spaceOverdueBacklog();
            }

            $schedule->register();
        }
    }

    public function middlewares()
    {
        return [
            'nonce' => NonceCheckerMiddleware::class,
        ];
    }

    public function getMiddleware($name)
    {
        if (isset($this->_registeredMiddleware[$name])) {
            return $this->_registeredMiddleware[$name];
        }

        $middlewares = $this->middlewares();
        if (isset($middlewares[$name]) && class_exists($middlewares[$name]) && method_exists($middlewares[$name], 'handle')) {
            $this->_registeredMiddleware[$name] = new $middlewares[$name]();
        } else {
            return false;
        }

        return $this->_registeredMiddleware[$name];
    }

    /**
     * Instantiate the Provider class.
     */
    public function registerProviders()
    {
        if (RequestType::is('admin')) {
            new Layout();
        }

        new HookProvider();
    }

    public static function maybeMigrateDB()
    {
        if (!Config::userHasAccess()) {
            return;
        }

        if (version_compare(Config::getOption('db_version'), Config::DB_VERSION, '<') || version_compare(Config::getOption('version'), Config::VERSION, '<')) {
            MigrationHelper::migrate(InstallerProvider::migration());
        }
    }

    /**
     * Retrieves the main instance of the plugin.
     *
     * @since 1.0.0-alpha
     *
     * @return Plugin plugin main instance
     */
    public static function instance()
    {
        return static::$_instance;
    }

    /**
     * Loads the plugin main instance and initializes it.
     *
     * @return bool True if the plugin main instance could be loaded, false otherwise
     *
     * @since 1.0.0-alpha
     */
    public static function load()
    {
        if (static::$_instance !== null) {
            return false;
        }

        static::$_instance = new static();

        return true;
    }

    /**
     * Whether this request is one where the missed-post sweep earns its cost.
     *
     * The sweep reads and rewrites cron state for every one-shot schedule on the
     * site, and the spacing pass writes the cron option once per event it moves.
     * On a front-end hit that is a per-visitor tax — on a site with a few
     * hundred schedules, a noticeable one — and it buys nothing there: what the
     * sweep protects happens on a cron request (wp-cron.php dispatches due hooks
     * after plugins_loaded, so re-queueing still lands in the same run) and what
     * it reports is read in wp-admin. A stock WordPress install spawns its cron
     * as a loopback request of its own, so visitor traffic still drives it.
     *
     * So: always on cron and WP-CLI, on an actual admin screen no more than once
     * a minute, and never on AJAX — which is_admin() also answers true for.
     *
     * @return bool
     */
    private static function shouldRunScheduleMaintenance()
    {
        // Cron is where the sweep does its real work — wp-cron.php dispatches
        // due hooks after plugins_loaded, so re-queueing here still lands in the
        // run that triggered it — and WP serialises cron requests with a lock of
        // its own, so neither is throttled.
        if (self::isCronRequest() || (\defined('WP_CLI') && WP_CLI)) {
            return true;
        }

        // is_admin() is true for admin-ajax.php, which is not an admin screen:
        // it is every heartbeat tick and every AJAX call any other plugin makes,
        // the front-end ones included. Rewriting cron state on those is a tax on
        // requests where nobody is reading the result.
        if (!is_admin() || wp_doing_ajax()) {
            return false;
        }

        return self::claimScheduleMaintenance();
    }

    /**
     * Let one admin page load at a time run the sweep.
     *
     * Two things this buys. A dashboard with several tabs open stops repeating
     * the same read-and-rewrite on every load. And the spacing pass is the only
     * thing here that writes the cron option, which WordPress rewrites whole
     * with no compare-and-set — fewer concurrent writers, fewer lost updates.
     *
     * The failure it cannot rule out is benign: an event whose move is lost
     * keeps the overdue slot it already had, so it still fires, just without the
     * spacing. Never taken on a cron request, where skipping the pass would cost
     * the spacing on the very run that needed it.
     *
     * @return bool
     */
    private static function claimScheduleMaintenance()
    {
        $lock = Config::VAR_PREFIX . 'schedule_maintenance';

        if (get_transient($lock)) {
            return false;
        }

        set_transient($lock, time(), MINUTE_IN_SECONDS);

        return true;
    }

    /**
     * @return bool
     */
    private static function isCronRequest()
    {
        return \function_exists('wp_doing_cron')
            ? wp_doing_cron()
            : (\defined('DOING_CRON') && DOING_CRON);
    }

    private function cleanupLegacyTrackingOption()
    {
        if (!version_compare(Config::getOption('version'), self::MAX_FREE_TELEMETRY_VERSION, '<')) {
            return;
        }

        if (class_exists(ProConfig::class)) {
            return;
        }

        $trackingOptionKeys = [
            'allow_tracking',
            'tracking_notice_dismissed',
            'tracking_skipped',
            'tracking_opt_in',
        ];

        foreach ($trackingOptionKeys as $optionKey) {
            // Use null as default so falsy values are still treated as existing options.
            $optionValue = Config::getOption($optionKey, null);
            if ($optionValue === null) {
                continue;
            }

            delete_option(Config::withPrefix($optionKey));
        }
    }
}
