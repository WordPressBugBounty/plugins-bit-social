<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Model\Account;

final class BitSocialAnalyticsController
{
    public function filterTrackingData($additional_data)
    {
        $accountsData = Account::get(['platform', 'account_type', 'status']);

        $additional_data['accounts'] = json_decode(wp_json_encode($accountsData));

        // Add plugin data to the tracking data

        return $additional_data;
    }
}
