<?php

use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\HTTP\Controllers\AutoPostController;

if (!defined('ABSPATH')) {
    exit;
}

// Hooks::addAction('wp_after_insert_post', function ($postId, $post, $update, $postBefore) {
//     (new AutoPostController())->publishWpPost($postId, $post, $update, $postBefore);
//     // error_log('Auto post hook called');
// }, 10, 4);

Hooks::addAction('wp_after_insert_post', [new AutoPostController(), 'publishWpPost'], 10, 4);

// A block editor publish whose meta-box save never arrived.
Hooks::addAction(AutoPostController::FINALIZE_HOOK, [new AutoPostController(), 'finalizePendingAutoPost']);

// Events queued by the previous build's one-minute settle delay.
Hooks::addAction(AutoPostController::SETTLE_HOOK, [new AutoPostController(), 'publishAfterSettle']);

// Lets a block editor publish know whether a meta-box save is coming.
Hooks::addFilter('filter_block_editor_meta_boxes', [new AutoPostController(), 'rememberEditorMetaBoxes'], PHP_INT_MAX);
