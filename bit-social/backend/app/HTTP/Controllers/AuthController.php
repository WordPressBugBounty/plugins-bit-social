<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Requests\AuthorizeRequest;
use BitApps\Social\HTTP\Services\Factories\AuthServiceFactory;

class AuthController
{
    private $authFactory;

    public function __construct()
    {
        $authFactory = new AuthServiceFactory();
        $this->authFactory = $authFactory;
    }

    public function authorize(AuthorizeRequest $request)
    {
        $authConfig = $request->all()['config'];
        $platform = $authConfig['platform'];
        $authType = $authConfig['authType'];

        $authService = $this->authFactory->createAuthService($platform, $authType);
        if (property_exists($authService, 'status') && $authService->status === 'error') {
            return Response::error($authService);
        }

        $response = $authService->authHandler($request);

        if (isset($response->status) && $response->status === 'error') {
            return Response::error($response);
        }

        return Response::success($response);
    }

    public function aiAuthorize(Request $request)
    {
        // Connecting an AI platform (storing its API key) is an admin task,
        // matching account provisioning: a non-admin role granted plugin access
        // uses the AI prompts but does not manage the platform connection.
        if (!current_user_can('manage_options')) {
            return Response::error(['message' => __('You are not permitted to connect AI platforms.', 'bit-social')])->httpStatus(403);
        }

        $body = $request->all();

        $platform = $body['platform'];
        $authType = $body['authType'];

        $authService = $this->authFactory->createAiAuthService($platform, $authType);
        if (property_exists($authService, 'status') && $authService->status === 'error') {
            return Response::error($authService);
        }

        $response = $authService->authHandler($request);

        if (isset($response->status) && $response->status === 'error') {
            return Response::error($response);
        }

        return Response::success($response);
    }
}
