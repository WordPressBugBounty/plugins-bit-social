<?php

namespace BitApps\Social\HTTP\Services\Social\FacebookService;

use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\Model\Account;
use stdClass;

class RefreshFacebookService
{
    /**
     * Long lived user tokens last 60 days; renew them with a margin.
     */
    private const USER_TOKEN_LIFETIME = 50 * 24 * 60 * 60;

    private $httpHandler;

    private $baseUrl = 'https://graph.facebook.com/';

    private $baseServerUrl = 'https://auth-apps.bitapps.pro/apps/';

    private $version = 'v16.0';

    private $facebookAppIdUrl;

    private $facebookAppSecretUrl;

    private $refreshTokenUrl;

    public function __construct()
    {
        $this->httpHandler = new HttpClient();
        $this->facebookAppIdUrl = $this->baseServerUrl . 'facebook';
        $this->facebookAppSecretUrl = $this->baseServerUrl . 'secret';
        $this->refreshTokenUrl = $this->baseUrl . $this->version . '/oauth/access_token?';
    }

    public function tokenExpiryCheck($accessToken, $generates_on, $accountType = '')
    {
        if (!$accessToken && !$generates_on) {
            return false;
        }

        if ($this->needsRefresh($generates_on, $accountType)) {
            $refreshToken = $this->refreshToken($accessToken);

            // refreshToken() returns false when the app credentials or the
            // exchange call fail, so the object check has to come first.
            if (!\is_object($refreshToken) || !property_exists($refreshToken, 'access_token')) {
                return false;
            }

            return $this->tokenData($refreshToken->access_token, $refreshToken->generates_on);
        }

        return $this->tokenData($accessToken, $generates_on);
    }

    public function refreshToken($accessToken)
    {
        $facebookAppIdData = $this->httpHandler->request($this->facebookAppIdUrl, 'GET', []);
        $facebookAppId = $facebookAppIdData->clientId;
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $getSecretParams = [
            'client_id' => $facebookAppId,
            'platform'  => 'facebook',
        ];

        $facebookAppSecret = $this->httpHandler->request($this->facebookAppSecretUrl, 'POST', json_encode($getSecretParams), $headers);
        if (empty($facebookAppSecret->clientSecret)) {
            return false;
        }
        $facebookAppSecret = $facebookAppSecret->clientSecret;
        $longTimeAccessTokenParams = [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $facebookAppId,
            'client_secret'     => $facebookAppSecret,
            'fb_exchange_token' => $accessToken,
        ];

        $refreshTokenUrlWithParams = $this->refreshTokenUrl . http_build_query($longTimeAccessTokenParams);
        $apiResponse = $this->httpHandler->request($refreshTokenUrlWithParams, 'GET', []);
        if (!property_exists($apiResponse, 'access_token')) {
            return false;
        }

        return $this->tokenData($apiResponse->access_token, time());
    }

    public function saveRefreshedToken($account_detail)
    {
        $accountId = $account_detail->account_id;
        if (empty($accountId)) {
            return;
        }
        $account = Account::findOne(['account_id' => $accountId]);
        $account->update(['details' => $account_detail])->save();
    }

    /**
     * The shape callers expect back from a token check or refresh.
     *
     * @param string $accessToken
     * @param int    $generatesOn
     *
     * @return stdClass
     */
    private function tokenData($accessToken, $generatesOn)
    {
        $data = new stdClass();
        $data->access_token = $accessToken;
        $data->generates_on = $generatesOn;

        return $data;
    }

    /**
     * Page tokens are issued by /me/accounts against a long lived user token
     * and do not expire. Passing one to the fb_exchange_token grant returns a
     * user token, which Facebook then rejects when publishing to the page, so
     * pages are never refreshed.
     *
     * @param int    $generates_on
     * @param string $accountType  'page' or 'group'
     *
     * @return bool
     */
    private function needsRefresh($generates_on, $accountType)
    {
        if ($accountType === 'page') {
            return false;
        }

        return (\intval($generates_on) + self::USER_TOKEN_LIFETIME) < time();
    }
}
