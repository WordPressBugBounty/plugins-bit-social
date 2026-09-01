<?php

namespace BitApps\Social\HTTP\Services\Social\LinkedinService;

use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Account;
use BitApps\Social\Model\Log;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\Hash;
use BitApps\Social\Utils\LogRun;

class LinkedinRefreshTokenService
{
    use Common, LoggerTrait;

    private $httpHandler;

    private $baseUrl = 'https://www.linkedin.com/oauth/';

    private $version = 'v2';

    private $refreshTokenUrl;

    private $accountId;

    public function __construct()
    {
        $this->httpHandler = new HttpClient();
        $this->refreshTokenUrl = $this->baseUrl . $this->version . '/accessToken';
    }

    public function tokenExpiryCheck($client_id, $client_secret, $refresh_token, $account_id)
    {
        $this->accountId = $account_id;

        return $this->refreshAccessToken($client_id, $client_secret, $refresh_token);
    }

    /**
     * Returns the new access token, or false when the refresh fails, so the
     * caller can keep the stored token instead of publishing with nothing.
     *
     * @param mixed $client_id
     * @param mixed $client_secret
     * @param mixed $refresh_token
     */
    public function refreshAccessToken($client_id, $client_secret, $refresh_token)
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

        // A failed refresh (network error, revoked grant, expired refresh
        // token) must never be written over the stored pair: encrypting the
        // missing fields would permanently brick the connection.
        if (!\is_object($tokenResponse) || empty($tokenResponse->access_token)) {
            $this->logRefreshFailure($tokenResponse);

            // The grant itself is gone (revoked, or the refresh token aged
            // out after a year), so every later publish fails the same way
            // until the user re-authorizes. Warn on the account card.
            Account::flagNeedsReconnect('linkedin', $this->accountId, __('LinkedIn could not renew the access token.', 'bit-social'));

            return false;
        }

        $this->saveRefreshedToken($tokenResponse);

        return $tokenResponse->access_token;
    }

    public function saveRefreshedToken($tokenResponse)
    {
        if (empty($this->accountId)) {
            return;
        }
        $account = Account::findOne(['account_id' => $this->accountId, 'platform' => 'linkedin']);

        if (!$account || empty($account->details)) {
            return;
        }

        $details = Account::withoutReconnectFlag($account->details);
        $details->access_token = Hash::encrypt($tokenResponse->access_token);
        $details->expires_in = time() + (int) ($tokenResponse->expires_in ?? 0);

        // Keep the stored refresh token unless the response rotates it, so a
        // partial response cannot wipe the only working token.
        if (!empty($tokenResponse->refresh_token)) {
            $details->refresh_token = Hash::encrypt($tokenResponse->refresh_token);
        }
        if (!empty($tokenResponse->refresh_token_expires_in)) {
            $details->refresh_token_expires_in = time() + (int) $tokenResponse->refresh_token_expires_in;
        }

        $account->update(['details' => $details])->save();
    }

    private function logRefreshFailure($response)
    {
        Log::insert([
            'schedule_id' => null,
            'platform'    => 'linkedin',
            'status'      => Log::status['FAIL'],
            // A standalone event is a run of one, but it still has to carry an
            // id: a null here makes the Logs screen read every row the slow way.
            'run_id'  => LogRun::current(),
            'details' => wp_json_encode([
                'account_id' => $this->accountId,
                'message'    => 'LinkedIn access token refresh failed; the stored token was kept. Reconnect the account if publishing stays unauthorized.',
                'response'   => $response,
            ]),
        ]);
    }
}
