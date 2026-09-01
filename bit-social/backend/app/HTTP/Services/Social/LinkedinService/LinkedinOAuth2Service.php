<?php

namespace BitApps\Social\HTTP\Services\Social\LinkedinService;

use BitApps\Social\Deps\BitApps\WPKit\Helpers\Arr;
use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Account;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\ConnectError;
use BitApps\SocialPro\Config as ProConfig;

class LinkedinOAuth2Service
{
    use Common, LoggerTrait;

    private $httpHandler;

    private $baseUrl = 'https://www.linkedin.com/';

    private $apiBaseUrl = 'https://api.linkedin.com/';

    private $version = 'v2/';

    public function __construct()
    {
        $this->httpHandler = new HttpClient();
    }

    public function authHandler($request)
    {
        $body = $request->body();
        $client_id = $body['payload']['client_id'];
        $client_secret = $body['payload']['client_secret'];
        $redirect_uri = $body['payload']['redirect_uri'];
        $code = $body['payload']['code'];

        $tokenInfo = $this->getAccessToken($client_id, $client_secret, $redirect_uri, $code);

        if (is_wp_error($tokenInfo)) {
            return Response::error(ConnectError::unreachable($tokenInfo, 'LinkedIn'));
        }

        if (\is_object($tokenInfo) && property_exists($tokenInfo, 'error') && $tokenInfo->error) {
            return Response::error(!empty($tokenInfo->error_description) ? $tokenInfo->error_description : $tokenInfo->error);
        }

        if (!\is_object($tokenInfo) || empty($tokenInfo->access_token)) {
            return (object) ['status' => 'error', 'message' => 'Access token is not valid, please authorize again!'];
        }
        $access_token = $tokenInfo->access_token;

        $userAccount = $this->getUserAccount($access_token);

        if (empty($userAccount['account_id'])) {
            return (object) ['status' => 'error', 'message' => 'Fetching the LinkedIn profile failed, please authorize again!'];
        }

        // Older versions keyed the row by the display name, which broke
        // reconnect matching whenever the profile was renamed. Re-key any such
        // row to the stable member id before the accounts are organized.
        $this->migrateLegacyProfileAccountId($userAccount);

        $tokenDetail = Helper::organizeToken($client_id, $client_secret, $tokenInfo);

        $companies = null;

        if (class_exists(ProConfig::class) && is_plugin_active(ProConfig::get('BASENAME'))) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is prefixed via ProConfig::VAR_PREFIX constant.
            $companies = apply_filters(ProConfig::VAR_PREFIX . 'get_linkedin_company_page', $this->apiBaseUrl, $this->version, $access_token);
        }

        return $this->accountOrganize($userAccount, $tokenDetail, $companies);
    }

    public function getAccessToken($client_id, $client_secret, $redirect_uri, $code)
    {
        $accessTokenUrl = $this->baseUrl . 'oauth/' . $this->version . 'accessToken';

        $header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        $params = [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri'  => $redirect_uri
        ];

        return $this->httpHandler->request($accessTokenUrl, 'POST', $params, $header);
    }

    public function getUserAccount($accessToken)
    {
        $header = [
            'X-RestLi-Protocol-Version' => '2.0.0',
            'Connection'                => 'Keep-Alive',
            'X-li-format'               => 'json',
            'Content-Type'              => 'application/x-www-form-urlencoded',
        ];

        $user_profile_url = 'https://api.linkedin.com/v2/userinfo?oauth2_access_token=' . $accessToken;
        $person = $this->httpHandler->request($user_profile_url, 'GET', null, $header);

        if (!\is_object($person) || empty($person->sub)) {
            return [];
        }

        return [
            'account_type' => 'profile',
            'urn'          => "urn:li:person:{$person->sub}",
            // The stable member id, not the display name: names change and
            // collide, which broke reconnect matching and token refresh lookups.
            'account_id'   => $person->sub,
            'account_name' => $person->name ?? $person->sub,
            'icon'         => isset($person->picture) ? $person->picture : '',
        ];
    }

    /**
     * Re-keys profile rows stored under the LinkedIn display name (the
     * account_id of connections made by older versions) to the member id,
     * matching them by the urn kept in details. Keeps reconnects updating the
     * existing row - and its schedules - instead of creating a duplicate.
     *
     * @param mixed $userAccount
     */
    public function migrateLegacyProfileAccountId($userAccount)
    {
        $legacyRows = Account::where('platform', 'linkedin')
            ->where('details', 'like', '%"urn":"' . $userAccount['urn'] . '"%')
            ->get(['id', 'account_id']);

        if (!\is_array($legacyRows)) {
            $legacyRows = empty($legacyRows) ? [] : [$legacyRows];
        }

        foreach ($legacyRows as $row) {
            if ((string) $row->account_id === (string) $userAccount['account_id']) {
                continue;
            }

            $account = Account::findOne(['id' => $row->id]);

            if (!$account) {
                continue;
            }

            $details = $account->details;

            if (\is_object($details)) {
                $details->account_id = $userAccount['account_id'];
            }

            $account->update([
                'account_id' => $userAccount['account_id'],
                'profile_id' => $userAccount['account_id'],
                'details'    => $details,
            ])->save();
        }
    }

    public function accountOrganize($userAccount, $tokenDetail, $companies)
    {
        $userAccount = array_merge($userAccount, $tokenDetail);

        if (!empty($companies)) {
            foreach ($companies as $company) {
                $companiesData[] = array_merge($company, $tokenDetail);
            }
            $allAccounts = array_merge([$userAccount], $companiesData);
        } else {
            $allAccounts = [$userAccount];
        }

        $arr = new Arr();
        $accountIds = $arr->pluck($allAccounts, 'account_id');

        $existAccountIds = Account::whereIn('account_id', $accountIds)->get('account_id');

        if (!\is_array($existAccountIds)) {
            $existAccountIds = [];
        }

        $existAccountIds = $arr->pluck($existAccountIds, 'account_id');

        if (\is_array($allAccounts)) {
            foreach ($allAccounts as $account) {
                $isConnected = \in_array($account['account_id'], $existAccountIds);

                $data['profile_id'] = $account['account_id'];
                $data['account_id'] = $account['account_id'];
                $data['account_name'] = $account['account_name'];
                $data['details'] = json_encode($account);
                $data['platform'] = 'linkedin';
                $data['account_type'] = Account::accountType['DEFAULT'];
                $data['status'] = Account::ACCOUNT_STATUS['active'];
                $allAccount[] = ['account' => $data, 'isConnected' => $isConnected];
            }
        }
        if (empty($allAccount)) {
            return Response::error('Something went wrong');
        }

        return $allAccount;
    }
}
