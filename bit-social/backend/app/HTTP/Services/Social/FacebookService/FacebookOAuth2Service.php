<?php

namespace BitApps\Social\HTTP\Services\Social\FacebookService;

use BitApps\Social\Deps\BitApps\WPKit\Helpers\Arr;
use BitApps\Social\Deps\BitApps\WPKit\Http\Client\HttpClient;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Services\Interfaces\AuthServiceInterface;
use BitApps\Social\HTTP\Services\Traits\LoggerTrait;
use BitApps\Social\Model\Account;
use BitApps\Social\Utils\Common;
use BitApps\Social\Utils\ConnectError;
use BitApps\Social\Utils\MetaGraph;

class FacebookOAuth2Service implements AuthServiceInterface
{
    use Common, LoggerTrait;

    private $httpHandler;

    private $baseUrl = 'https://graph.facebook.com/';

    private $version = MetaGraph::VERSION;

    // all urls
    private $shortAccessTokenUrl;

    private $longTimeAccessTokenUrl;

    private $userAccountInfoUrl;

    private $getAccountsUrl;

    private $getAllGroupUrl;

    public function __construct()
    {
        $this->httpHandler = new HttpClient();
        $this->shortAccessTokenUrl = $this->baseUrl . $this->version . '/oauth/access_token?';
        $this->longTimeAccessTokenUrl = $this->baseUrl . $this->version . '/oauth/access_token?';
        // Version every call: an unversioned Graph URL is served by the
        // oldest version Meta still supports, which drifts out from under us.
        $this->userAccountInfoUrl = $this->baseUrl . $this->version . '/me?';
        $this->getAccountsUrl = $this->baseUrl . $this->version . '/me/accounts?';
        $this->getAllGroupUrl = $this->baseUrl . $this->version . '/me/groups?';
    }

    // TODO: encode only query params value. should  refactor
    public function urlParamEncode($url)
    {
        // Parse the URL to get the query string
        $queryString = wp_parse_url($url, PHP_URL_QUERY);

        // A redirect URI on pretty permalinks has no query string at all, and
        // parse_str() is deprecated on null as of PHP 8.1.
        if (!\is_string($queryString) || $queryString === '') {
            return $url;
        }

        // Separate the query string into key-value pairs
        parse_str($queryString, $params);

        if (isset($params['rest_route'])) {
            // Encode each value in the parameters array
            foreach ($params as $key => $value) {
                $params[$key] = urlencode($value);
            }

            // Rebuild the query string

            $newQueryString = 'rest_route=' . $params['rest_route'];

            // Reconstruct the URL with the encoded query string
            return str_replace($queryString, $newQueryString, $url);
        }

        return $url;
    }

    // TODO: handle response properly. Follow pattern from twitter service.
    public function authHandler($request)
    {
        $body = $request->body();
        $app_id = $body['payload']['client_id'];
        $client_secret = $body['payload']['client_secret'];
        $redirect_uri = $body['payload']['redirect_uri'];
        $code = $body['payload']['code'];

        $shortAccessTokenParams = [
            'client_id'     => $app_id,
            'client_secret' => $client_secret,
            'redirect_uri'  => $this->urlParamEncode($redirect_uri),
            'code'          => $code,
        ];

        $shortAccessTokenUrlWithParams = $this->shortAccessTokenUrl . http_build_query($shortAccessTokenParams);

        $shortAccessTokenResponse = $this->httpHandler->request($shortAccessTokenUrlWithParams, 'GET', []);

        $shortAccessTokenError = $this->tokenError($shortAccessTokenResponse);
        if ($shortAccessTokenError !== null) {
            return $shortAccessTokenError;
        }

        $longTimeAccessTokenParams = [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $app_id,
            'client_secret'     => $client_secret,
            'fb_exchange_token' => $shortAccessTokenResponse->access_token,
            'redirect_uri'      => $this->urlParamEncode($redirect_uri),
        ];

        $longTimeAccessTokenUrlWithParams = $this->longTimeAccessTokenUrl . http_build_query($longTimeAccessTokenParams);

        $longTimeAccessTokenData = $this->httpHandler->request($longTimeAccessTokenUrlWithParams, 'GET', []);

        $longTimeAccessTokenError = $this->tokenError($longTimeAccessTokenData);
        if ($longTimeAccessTokenError !== null) {
            return $longTimeAccessTokenError;
        }

        $longTimeAccessToken = $longTimeAccessTokenData->access_token;
        $appsecret_proof = hash_hmac(
            'sha256',
            $longTimeAccessToken,
            $client_secret
        );

        $userInfoData = self::getUserInfo($longTimeAccessToken, $appsecret_proof);

        if (is_wp_error($userInfoData) || !\is_object($userInfoData) || empty($userInfoData->id)) {
            return (object) ['status' => 'error', 'message' => 'Fetching the Facebook profile failed, please authorize again!'];
        }

        $accountsPages = $this->accountsPages($longTimeAccessToken, $appsecret_proof);
        $accountsGroups = $this->accountsGroups($longTimeAccessToken, $appsecret_proof);

        $pageAndGroup = $this->organizePageAndGroup(
            $this->listOf($accountsPages),
            $this->listOf($accountsGroups),
            $userInfoData,
            $longTimeAccessToken
        );

        // A profile that manages no pages and belongs to no groups has nothing
        // to offer, and looking its ids up would run `IN ()`.
        if (empty($pageAndGroup)) {
            return (object) ['status' => 'error', 'message' => 'No Facebook page or group was found for this profile.'];
        }

        if (\is_array($pageAndGroup)) {
            $allAccount = [];
            $arr = new Arr();

            $accountIds = $arr->pluck($pageAndGroup, 'account_id');
            $existAccountIds = Account::whereIn('account_id', $accountIds)->get('account_id');

            if (!\is_array($existAccountIds)) {
                $existAccountIds = [];
            }

            $existAccountIds = $arr->pluck($existAccountIds, 'account_id');

            foreach ($pageAndGroup as $item) {
                $isConnected = \in_array($item['account_id'], $existAccountIds);

                $data['profile_id'] = $userInfoData->id;
                $data['account_id'] = $item['account_id'];
                $data['account_name'] = $item['account_name'];
                $data['details'] = json_encode($item);
                $data['platform'] = 'facebook';
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

    public function getUserInfo($longTimeAccessToken, $appsecret_proof)
    {
        $userInfoQuery = [
            'fields'          => 'id,name,email',
            'access_token'    => $longTimeAccessToken,
            'appsecret_proof' => $appsecret_proof
        ];

        $userAccountInfoUrlWithParams = $this->userAccountInfoUrl . http_build_query($userInfoQuery);

        return $this->httpHandler->request($userAccountInfoUrlWithParams, 'GET', []);
    }

    public function accountsPages($longTimeAccessToken, $appsecret_proof)
    {
        $accountQuery = [
            'fields'          => 'access_token,category,name,id',
            'limit'           => '100',
            'access_token'    => $longTimeAccessToken,
            'appsecret_proof' => $appsecret_proof
        ];

        $getAccountsUrlWithParams = $this->getAccountsUrl . http_build_query($accountQuery);

        return $this->httpHandler->request($getAccountsUrlWithParams, 'GET', []);
    }

    public function accountsGroups($longTimeAccessToken, $appsecret_proof)
    {
        $accountQuery = [
            'fields'          => 'name,privacy,id,icon,cover{source},administrator',
            'limit'           => '100',
            'access_token'    => $longTimeAccessToken,
            'appsecret_proof' => $appsecret_proof
        ];

        $getAllGroupUrlWithParams = $this->getAllGroupUrl . http_build_query($accountQuery);

        return $this->httpHandler->request($getAllGroupUrlWithParams, 'GET', []);
    }

    public function fetchPageProfilePic($accessToken, $accountId)
    {
        $params = [
            'fields'       => 'picture',
            'access_token' => $accessToken
        ];

        $pagePicUrl = $this->baseUrl . "{$this->version}/{$accountId}?";
        $pagePicUrlWithParams = $pagePicUrl . http_build_query($params);

        $response = $this->httpHandler->request($pagePicUrlWithParams, 'GET', []);

        return $response->picture->data->url;
    }

    public function organizePageAndGroup($accountsPages, $accountsGroups, $userInfoData, $longTimeAccessToken)
    {
        $pages = [];
        $groups = [];

        foreach ($accountsPages as $page) {
            $pages[] = [
                'user_name'    => $userInfoData->name,
                'account_id'   => $page->id,
                'account_name' => $page->name,
                'account_type' => 'page',
                'category'     => $page->category,
                'icon'         => $this->fetchPageProfilePic($longTimeAccessToken, $page->id),
                'access_token' => $page->access_token,
                'generates_on' => time()
            ];
        }

        foreach ($accountsGroups as $group) {
            $groups[] = [
                'user_name'     => $userInfoData->name,
                'account_id'    => $group->id,
                'account_name'  => $group->name,
                'privacy'       => $group->privacy,
                'account_type'  => 'group',
                'icon'          => $group->icon,
                'administrator' => $group->administrator,
                'access_token'  => $longTimeAccessToken,
                'generates_on'  => time()
            ];
        }

        return array_merge($pages, $groups);
    }

    /**
     * Facebook answers a failed exchange with an `error` object, and an
     * unreachable Graph with a WP_Error -- neither carries a token, and every
     * step after this one assumes one is there.
     *
     * @param mixed $response
     *
     * @return null|object null when the response does carry a token
     */
    private function tokenError($response)
    {
        if (is_wp_error($response)) {
            return (object) ['status' => 'error', 'message' => ConnectError::unreachable($response, 'Facebook')];
        }

        if (\is_object($response) && !empty($response->error)) {
            $error = $response->error;
            $message = \is_object($error) && !empty($error->message) ? $error->message : 'Authorization failed, please try again!';

            return (object) ['status' => 'error', 'message' => $message];
        }

        if (!\is_object($response) || empty($response->access_token)) {
            return (object) ['status' => 'error', 'message' => 'Facebook did not return an access token, please authorize again!'];
        }
    }

    /**
     * Graph list endpoints answer `{ data: [...] }`; anything else (an error
     * object, a WP_Error) has no list to walk.
     *
     * @param mixed $response
     *
     * @return array
     */
    private function listOf($response)
    {
        if (!\is_object($response) || !isset($response->data) || !\is_array($response->data)) {
            return [];
        }

        return $response->data;
    }
}
