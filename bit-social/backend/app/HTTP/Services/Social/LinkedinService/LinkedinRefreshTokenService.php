<?php

namespace BitApps\Social\HTTP\Services\Social\LinkedinService;

use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Account;
use BitApps\Social\Utils\Common;
use stdClass;

class LinkedinRefreshTokenService
{
    use Common, LoggerTrait;

    private $httpHandler;

    private $baseUrl = 'https://www.linkedin.com/oauth/';

    private $version = 'v2';

    private $refreshTokenUrl;

    public function __construct()
    {
        $this->httpHandler = new HttpClient();
        $this->refreshTokenUrl = $this->baseUrl . $this->version . '/accessToken';
    }

    public function tokenExpiryCheck($client_id, $client_secret, $access_token, $expires_in, $refresh_token, $refresh_token_expires_in)
    {
        if (!$access_token && !$refresh_token) {
            return false;
        }

        if ((\intval($expires_in)) < time()) {
            return $this->refreshToken($client_id, $client_secret, $access_token, $refresh_token);
        }

        $data = new stdClass();
        $data->access_token = $access_token;
        $data->expires_in = $expires_in;
        $data->refresh_token = $refresh_token;
        $data->refresh_token_expires_in = $refresh_token_expires_in;

        return $data;
    }

    public function refreshToken($client_id, $client_secret, $access_token, $refresh_token)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $params = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ];

        $tokenResponse = $this->httpHandler->request($this->refreshTokenUrl, 'POST', $params, $headers);

        if (!property_exists($tokenResponse, 'access_token')) {
            return (object) ['status' => 'error', 'message' => 'Refresh token request failed!'];
        }

        $data = new stdClass();
        $data->access_token = $tokenResponse->access_token;
        $data->expires_in = time() + $tokenResponse->expires_in;
        $data->refresh_token = $tokenResponse->refresh_token;
        $data->refresh_token_expires_in = time() + $tokenResponse->refresh_token_expires_in;

        return $data;
    }

    public function saveRefreshedToken($account_detail)
    {
        $accountId = $account_detail->account_id;
        if (empty($accountId)) {
            return;
        }
        $account = Account::findOne(['account_id' => $accountId]);
        $account->update(['details' => $account_detail]);
    }
}
