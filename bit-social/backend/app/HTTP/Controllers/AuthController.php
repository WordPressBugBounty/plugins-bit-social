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

        $error = $this->asErrorEnvelope($response);
        if ($error !== null) {
            return $error;
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

        $error = $this->asErrorEnvelope($response);
        if ($error !== null) {
            return $error;
        }

        return Response::success($response);
    }

    /**
     * Turn a failed connection into an error envelope, or return null when the
     * service reported success.
     *
     * Most services signal failure with a { status: 'error', message } object,
     * but several return the framework's Response singleton instead. That has
     * no public `status`, so it used to fall through to Response::success() --
     * which then overwrote the singleton's own error state, and an unreachable
     * provider reached the browser as `{"status":"success","data":{}}`. Connect
     * then looked like it had silently done nothing.
     *
     * @param mixed $response
     *
     * @return null|Response
     */
    private function asErrorEnvelope($response)
    {
        if (isset($response->status) && $response->status === 'error') {
            return Response::error($response);
        }

        if (!\is_object($response) || !method_exists($response, 'getStatus') || !method_exists($response, 'getData')) {
            return;
        }

        // Read both before calling Response::error() below -- these are static
        // on a singleton, so emitting our own response overwrites them. The
        // pro services carry their own prefixed copy of the class, hence
        // resolving the call off the returned instance rather than self::.
        $serviceClass = \get_class($response);
        $status = \call_user_func([$serviceClass, 'getStatus']);
        $data = \call_user_func([$serviceClass, 'getData']);

        if ($status !== Response::ERROR) {
            return;
        }

        return Response::error(
            \is_scalar($data) ? (object) ['status' => 'error', 'message' => $data] : $data
        );
    }
}
