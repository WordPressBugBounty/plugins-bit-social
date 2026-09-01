<?php

namespace BitApps\Social\HTTP\Controllers;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Http\Request\Request;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;

class SocialTemplateController
{
    public $defaultTemplateSettings = [
        'facebook' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'linkedin' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'twitter' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'pinterest' => [
            'postingType' => 'isFeaturedImage',
            'content'     => '{post_title}',
            'trimMessage' => true,
            'isLinkCard'  => false
        ],
        'discord' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'tumblr' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],

        'googleBusinessProfile' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'button'      => 'none',
            'trimMessage' => true
        ],
        'threads' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'comment'     => '',
            'topic'       => '',
            'trimMessage' => true
        ],
        'instagram' => [
            'postingType' => 'isFeaturedImage',
            'content'     => '{post_title}',
            'comment'     => '',
            'trimMessage' => true
        ],
        'bluesky' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'comment'     => '',
            'trimMessage' => true
        ],
        'line' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'telegram' => [
            'postingType' => 'onlyMessage',
            'content'     => '{post_title}',
            'trimMessage' => true
        ],
        'tiktok' => [
            'allowComment' => true,
            'content'      => '{post_title}',
            'duet'         => false,
            'postingType'  => 'onlyMessage',
            'privacyLevel' => 'PUBLIC_TO_EVERYONE',
            'stitch'       => false,
            'trimMessage'  => true
        ],
    ];

    public function socialTemplates()
    {
        return Response::success($this->getSocialTemplates());
    }

    public function update(Request $request)
    {
        Config::updateOption('templates_settings', $request->socialTemplates);

        return Response::success('Social template updated.');
    }

    public function getSocialTemplates()
    {
        $templateSettings = Config::getOption('templates_settings');

        if (!$templateSettings) {
            Config::updateOption('templates_settings', $this->defaultTemplateSettings);

            $templateSettings = $this->defaultTemplateSettings;
        }

        if (!\is_array($templateSettings)) {
            $templateSettings = [];
        }

        $normalized = $templateSettings;

        foreach ($this->defaultTemplateSettings as $platform => $defaults) {
            $stored = $normalized[$platform] ?? null;

            // A platform added after the option was saved, or a corrupted entry
            // (string / empty list), must still come back as a full settings map,
            // otherwise the settings page crashes on render or on first edit.
            if (!\is_array($stored) || array_values($stored) === $stored) {
                $stored = [];
            }

            $normalized[$platform] = $stored + $defaults;
        }

        if ($normalized !== $templateSettings) {
            Config::updateOption('templates_settings', $normalized);
        }

        return $normalized;
    }
}
