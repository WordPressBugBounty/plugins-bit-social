<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Requests\SettingsUpdateRequest;
use BitApps\Social\HTTP\Services\Schedule\CustomSchedule;

class SettingsController
{
    public $defaultSettings = [
        'settings' => [
            'cron'           => ['isDemoCronEnabled' => false],
            'missedSchedule' => [
                'autoPost' => CustomSchedule::DEFAULT_AUTOPOST_WINDOW,
                'schedule' => CustomSchedule::DEFAULT_SCHEDULE_WINDOW,
            ],
        ],
        'proSettings' => [
            'cron'         => ['isExternalCronEnabled' => false],
            'notification' => [
                'isEnabled'      => false,
                'recipients'     => '',
                'onFailure'      => false,
                'digestEnabled'  => false,
                'digestInterval' => 1,
                'digestUnit'     => 'week',
            ],
        ],
    ];

    public function getAllSettings()
    {
        // Deep-merge stored options over the defaults so every default key always
        // exists. Config::getOption only falls back to the default when the option
        // is entirely absent — once another feature saves e.g. pro_settings with
        // only { notification, access }, the "cron" branch would otherwise be
        // missing and the frontend crashes toggling External Cron.
        $storedSettings = (array) Config::getOption('settings', []);

        $settings = array_replace_recursive($this->defaultSettings['settings'], $storedSettings);

        // Read the windows from what was actually stored rather than the merged
        // result: the merge fills in the default shape, which would mask a value
        // saved under the earlier hours-only keys.
        $settings['missedSchedule'] = $this->sanitizeMissedScheduleWindows(
            isset($storedSettings['missedSchedule']) ? (array) $storedSettings['missedSchedule'] : []
        );

        $proSettings = array_replace_recursive(
            $this->defaultSettings['proSettings'],
            (array) Config::getOption('pro_settings', [])
        );

        $allSettings = [
            'settings'         => $settings,
            'proSettings'      => $proSettings,
            'smtp'             => $this->getSmtpStatus(),
            'notificationMeta' => $this->getNotificationMeta(),
            'cronInfo'         => $this->getCronInfo(),
        ];

        return Response::success($allSettings);
    }

    /**
     * Persist the free "settings" option. Merges over what is stored so a screen
     * can send only the branch it owns without wiping the others.
     */
    public function updateSettings(SettingsUpdateRequest $request)
    {
        $storedSettings = (array) Config::getOption('settings', []);
        $incoming = (array) $request->settings;

        $stored = array_replace_recursive($this->defaultSettings['settings'], $storedSettings);
        $merged = array_replace_recursive($stored, $incoming);

        // Windows are resolved from the raw stored branch plus whatever this
        // request sends, so an incoming value wins and an older hours-only value
        // is still understood — the merged copy would hide both behind defaults.
        $merged['missedSchedule'] = $this->sanitizeMissedScheduleWindows(array_replace(
            isset($storedSettings['missedSchedule']) ? (array) $storedSettings['missedSchedule'] : [],
            isset($incoming['missedSchedule']) ? (array) $incoming['missedSchedule'] : []
        ));

        Config::updateOption('settings', $merged);

        return Response::success($merged);
    }

    /**
     * Windows decide whether a late post is published or written off, so clamp
     * them rather than trusting the input: 0 means "never write anything off",
     * an unknown unit falls back to the default, and the upper bound keeps a
     * typo from parking a post in the queue for years.
     *
     * Stored per unit rather than normalised to seconds so the screen can show
     * back exactly what was entered — "2 days", not "48 hours".
     *
     * @param array $windows
     *
     * @return array
     */
    private function sanitizeMissedScheduleWindows($windows)
    {
        $defaults = [
            'autoPost' => CustomSchedule::DEFAULT_AUTOPOST_WINDOW,
            'schedule' => CustomSchedule::DEFAULT_SCHEDULE_WINDOW,
        ];

        $clean = [];

        foreach ($defaults as $key => $default) {
            $window = CustomSchedule::readWindow($windows, $key, $default);

            $unit = $window['unit'];
            $maxEvery = floor(CustomSchedule::MAX_WINDOW_SECONDS / CustomSchedule::DELAY_UNITS[$unit]);

            $clean[$key] = [
                'every' => max(0, min($maxEvery, (float) $window['every'])),
                'unit'  => $unit,
            ];
        }

        return $clean;
    }

    /**
     * Cron health for the Cron Setup screen: whether WP-Cron is enabled, when it
     * last processed a Bit Social schedule, the next queued run, and whether that
     * next run is overdue (a sign cron has stalled).
     *
     * Cron array timestamps are UTC, so overdue is compared against time() (UTC);
     * wp_date() converts to the site timezone for display.
     *
     * @return array
     */
    private function getCronInfo()
    {
        $format = trim(get_option('date_format') . ' ' . get_option('time_format'));
        if ($format === '') {
            $format = 'Y-m-d H:i';
        }

        $lastRun = (int) Config::getOption('last_cron_run', 0);
        $grace = (int) apply_filters(Config::VAR_PREFIX . 'cron_overdue_grace', 15 * MINUTE_IN_SECONDS);
        $now = time();

        // Walk the cron array once to find (a) our next queued run and (b) whether
        // ANY event is overdue past the grace — the same "missed cron" signal
        // WordPress Site Health uses to tell whether cron is actually firing.
        $nextTimestamp = 0;
        $hasMissedCron = false;
        $cronArray = _get_cron_array();
        if (\is_array($cronArray)) {
            foreach ($cronArray as $timestamp => $hooks) {
                if (($now - $timestamp) > $grace) {
                    $hasMissedCron = true;
                }
                foreach (array_keys($hooks) as $hook) {
                    if (strpos($hook, Config::VAR_PREFIX) === 0 && substr($hook, -10) === '_cron_exec'
                        && ($nextTimestamp === 0 || $timestamp < $nextTimestamp)) {
                        $nextTimestamp = $timestamp;
                    }
                }
            }
        }

        // Real health verdict (no loopback HTTP request needed):
        // - disabled: built-in WP-Cron is turned off (a server/external cron
        //   should be handling it — we can't judge from here).
        // - stalled:  built-in cron is on but events are overdue -> not firing.
        // - working:  built-in cron is on and nothing is overdue.
        if (!Config::get('WP_CRON_STATUS')) {
            $wpCronStatus = 'disabled';
        } elseif ($hasMissedCron) {
            $wpCronStatus = 'stalled';
        } else {
            $wpCronStatus = 'working';
        }

        return [
            'wpCronStatus'  => $wpCronStatus,
            'lastCronRun'   => $lastRun ? wp_date($format, $lastRun) : '',
            'nextCronRun'   => $nextTimestamp ? wp_date($format, $nextTimestamp) : '',
            'isCronOverdue' => $nextTimestamp > 0 && ($now - $nextTimestamp) > $grace,
        ];
    }

    /**
     * Last-sent timestamps for each notification type, formatted in the
     * site's date/time format and WordPress timezone.
     *
     * @return array
     */
    private function getNotificationMeta()
    {
        // Pro stores these under the bit_social_pro_ prefix; read them with the
        // free prefix + "pro_" so both plugins resolve the same option key.
        $failureLastSent = (int) Config::getOption('pro_failure_last_sent', 0);
        $digestLastSent = (int) Config::getOption('pro_digest_last_sent', 0);
        $missedLastSent = (int) Config::getOption('pro_missed_last_sent', 0);

        $format = trim(get_option('date_format') . ' ' . get_option('time_format'));
        if ($format === '') {
            $format = 'Y-m-d H:i';
        }

        return [
            'failureLastSent' => $failureLastSent ? wp_date($format, $failureLastSent) : '',
            'digestLastSent'  => $digestLastSent ? wp_date($format, $digestLastSent) : '',
            'missedLastSent'  => $missedLastSent ? wp_date($format, $missedLastSent) : '',
        ];
    }

    /**
     * Detects whether an SMTP mailer is configured (so email notifications can
     * actually be delivered) and surfaces install/activate links for Bit SMTP.
     *
     * @return array
     */
    private function getSmtpStatus()
    {
        if (!\function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $bitSmtpFile = 'bit-smtp/bit_smtp.php';
        $bitSmtpActive = is_plugin_active($bitSmtpFile);
        $bitSmtpInstalled = file_exists(WP_PLUGIN_DIR . '/' . $bitSmtpFile);

        // Any SMTP plugin (Bit SMTP, WP Mail SMTP, FluentSMTP, …) hooks phpmailer_init.
        $configured = $bitSmtpActive || (bool) has_action('phpmailer_init');

        return [
            'configured'       => $configured,
            'bitSmtpActive'    => $bitSmtpActive,
            'bitSmtpInstalled' => $bitSmtpInstalled,
            'installUrl'       => admin_url('plugin-install.php?s=Bit+SMTP&tab=search&type=term'),
            'pluginsUrl'       => admin_url('plugins.php'),
        ];
    }
}
