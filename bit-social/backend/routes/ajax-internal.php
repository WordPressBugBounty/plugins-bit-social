<?php

use BitApps\Social\Deps\BitApps\WPKit\Http\Router\Route;
use BitApps\Social\HTTP\Controllers\AutoPostController;

if (!defined('ABSPATH')) {
    exit;
}

// Self-triggered background routes used internally by the plugin (e.g. the
// wp_after_insert_post hook firing auto-post asynchronously). Auto-post is
// gated by Config::accessCapability() at the hook itself (see
// AutoPostController::publishWpPost), so these routes are only ever triggered
// on behalf of an authorized user; they are kept outside the capability-gated
// router so the loopback request doesn't additionally depend on that check,
// and each route re-validates the requester via nonce + post-level capability
// checks in its own controller method.
Route::noAuth()->group(function (): void {
    Route::post('auto-post-background-process', [AutoPostController::class, 'executeSocialPost']);
})->middleware('nonce');
