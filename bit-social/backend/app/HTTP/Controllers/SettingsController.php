<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Requests\SettingsUpdateRequest;

class SettingsController
{
    public $defaultSettings = [
        'settings' => [
            'cron' => ['isDemoCronEnabled' => false],
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
        $settings = Config::getOption('settings', $this->defaultSettings['settings']);
        $proSettings = Config::getOption('pro_settings', $this->defaultSettings['proSettings']);

        $allSettings = [
            'settings'         => $settings,
            'proSettings'      => $proSettings,
            'smtp'             => $this->getSmtpStatus(),
            'notificationMeta' => $this->getNotificationMeta(),
        ];

        return Response::success($allSettings);
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

        $format = trim(get_option('date_format') . ' ' . get_option('time_format'));
        if ($format === '') {
            $format = 'Y-m-d H:i';
        }

        return [
            'failureLastSent' => $failureLastSent ? wp_date($format, $failureLastSent) : '',
            'digestLastSent'  => $digestLastSent ? wp_date($format, $digestLastSent) : '',
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

    public function updateSettings(SettingsUpdateRequest $request)
    {
        // Update general settings
        // $settings = $request->settings;
    }
}
