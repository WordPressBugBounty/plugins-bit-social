<?php

namespace BitApps\Social\HTTP\Middleware;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;

final class NonceCheckerMiddleware
{
    public function handle(Request $request)
    {
        if (!$request->has('_ajax_nonce')) {
            return Response::error('Invalid token')->httpStatus(403);
        }

        $nonce = $request->_ajax_nonce;

        if (!\is_scalar($nonce)) {
            return Response::error('Invalid token')->httpStatus(403);
        }

        $nonce = sanitize_text_field(wp_unslash((string) $nonce));

        if (empty($nonce) || !wp_verify_nonce($nonce, Config::withPrefix('nonce'))) {
            return Response::error('Invalid token')->httpStatus(403);
        }

        return true;
    }
}
