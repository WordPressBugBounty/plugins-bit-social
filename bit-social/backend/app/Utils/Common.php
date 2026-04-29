<?php

namespace BitApps\Social\Utils;

use BitApps\Social\Config;
use BitApps\Social\HTTP\Controllers\AccountController;

trait Common
{
    public $platformLimitations = [
        'facebook'              => 63206,
        'linkedin'              => 3000,
        'pinterest'             => 497,
        'discord'               => 2000,
        'tumblr'                => 4096,
        'googleBusinessProfile' => 1500,
        'instagram'             => 2200,
        'bluesky'               => 300,
        'line'                  => 5000,
        'telegram'              => 1024,
        'threads'               => 500,
    ];

    /**
     * @param int|array $scheduleIdentifier
     *
     * @return bool|array
     */
    public function removeScheduleHook($scheduleIdentifier)
    {
        if (empty($scheduleIdentifier)) {
            return false;
        }

        if (\is_array($scheduleIdentifier)) {
            $clearedHooks = [];

            foreach ($scheduleIdentifier as $id) {
                $clearedHooks[$id] = $this->clearScheduleById($id);
            }

            return $clearedHooks;
        }

        return $this->clearScheduleById($scheduleIdentifier);
    }

    public function clearScheduleById($id)
    {
        $actionHook = Config::VAR_PREFIX . $id . '_cron_exec';

        return wp_clear_scheduled_hook($actionHook, [['schedule_id' => $id]]);
    }

    public function replacePostContent($postId, $template)
    {
        $link = '';
        $media = [];
        $video = [];

        if (!property_exists($template, 'trimMessage')) {
            $templateOptions = Config::getOption('templates_settings');
            $trimMessage = $templateOptions[$template->platform]['trimMessage'];
            $template->trimMessage = $trimMessage;
        }

        $smartTag = new SmartTag();
        $post = get_post($postId);

        if (!$post) {
            return '';
        }

        $content = $this->replaceTagValue($template->content, $post);
        $comment = isset($template->comment) ? $this->replaceTagValue($template->comment, $post) : null;

        $propertyTagMap = [
            'isFeaturedImage' => SmartTag::tag('featuredImageUrl'),
            'isProductImage'  => SmartTag::tag('productImageUrl'),
            'isLinkCard'      => SmartTag::tag('postLink'),
            'isAllImages'     => 'all_images',
            'isVideo'         => 'video',
            'isPromptImage'   => null,
            'isCustomImage'   => null
        ];

        if (property_exists($template, 'trimMessage') && $template->trimMessage) {
            $content = $this->clipText($template->platform, $content, $link);
            $comment = $this->clipText($template->platform, $comment, '');
        }

        // Handle properties and assign values - consolidate images into media array, video separate
        foreach ($propertyTagMap as $property => $smartTagValue) {
            if ((property_exists($template, $property) && $template->{$property})
                || (property_exists($template, 'postingType') && $template->postingType === $property)) {
                switch ($property) {
                    case 'isFeaturedImage':
                    case 'isProductImage':
                        $mediaValue = $smartTag->getSmartTagValue($smartTagValue, $post);
                        if (!empty($mediaValue)) {
                            $media[] = $mediaValue;
                        }

                        break;
                    case 'isLinkCard':
                        $link = $smartTag->getSmartTagValue($smartTagValue, $post);

                        break;
                    case 'isAllImages':
                        $allImages = $smartTag->getSmartTagValue($smartTagValue, $post);
                        if (!empty($allImages)) {
                            $media = array_merge($media, \is_array($allImages) ? $allImages : [$allImages]);
                        }

                        break;
                    case 'isVideo':
                        $videoValue = $smartTag->getSmartTagValue($smartTagValue, $post);
                        if (!empty($videoValue)) {
                            $video = \is_array($videoValue) ? $videoValue : [$videoValue];
                        }

                        break;
                    case 'isPromptImage':
                        // Get promptImage value from template and resolve it using getSmartTagValue
                        if (property_exists($template, 'promptImage') && !empty($template->promptImage)) {
                            $promptImageTag = $template->promptImage;
                            $promptImageValue = $smartTag->getSmartTagValue($promptImageTag, $post);

                            if (!empty($promptImageValue)) {
                                // If it's a valid URL, add it to media array
                                if (filter_var($promptImageValue, FILTER_VALIDATE_URL)) {
                                    $media[] = $promptImageValue;
                                }
                            }
                        }

                        break;
                    case 'isCustomImage':
                        // Get customImages array from template (array of URLs)
                        if (property_exists($template, 'customImages') && !empty($template->customImages)) {
                            $customImages = $template->customImages;
                            if (\is_array($customImages)) {
                                foreach ($customImages as $customImageUrl) {
                                    if (!empty($customImageUrl) && filter_var($customImageUrl, FILTER_VALIDATE_URL)) {
                                        $media[] = $customImageUrl;
                                    }
                                }
                            }
                        }

                        break;
                }
            }
        }

        // Remove empty and duplicate values from media array
        $media = array_values(array_filter(array_unique($media)));

        if (property_exists($template, 'platform') && !empty($template->platform)) {
            $media = $this->normalizeMediaForPlatform($media, $template->platform);
        }

        $formattedPostData = [
            'content' => $content,
            'media'   => $media,
            'video'   => $video,
            'link'    => $link,
            'comment' => $comment
        ];

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is prefixed via Config::withPrefix().
        return apply_filters(Config::withPrefix('post_data'), $formattedPostData, $postId);
    }

    public function replaceTagValue($text, $post)
    {
        $smartTag = new SmartTag();

        // Innermost `{...}` first: nested tags like `{prompt_49_[{post_title} ...]}` must not
        // have inner `{post_title}` replaced while the outer placeholder still uses that key in $text.
        $maxIterations = 1000;
        while ($maxIterations-- > 0 && preg_match('/\{([^{}]*)\}/', $text, $matches)) {
            $before = $text;
            $tag = $matches[1];
            $value = $smartTag->getSmartTagValue($tag, $post);

            // Remove placeholder if no value
            if ($value === '') {
                $text = str_replace('{' . $tag . '} ', '', $text);
            }

            $text = str_replace('{' . $tag . '}', $value, $text);

            if ($text === $before) {
                break;
            }
        }

        return html_entity_decode($text, ENT_QUOTES);
    }

    public function isPlatFormExists($accountId)
    {
        $accountInfo = AccountController::isExists($accountId);
        if (!$accountInfo) {
            return false;
        }

        $platform = $accountInfo->platform;
        $details = $accountInfo->details;

        $platformDir = 'BitApps\Social\HTTP\Services\Social';

        if (class_exists('BitApps\SocialPro\HTTP\Services\Social' . '\\' . ucfirst($platform) . 'Service' . '\\' . 'PostPublish' . ucfirst($platform) . 'Service') && $platform !== 'facebook' && $platform !== 'linkedin') {
            $platformDir = 'BitApps\SocialPro\HTTP\Services\Social';
        }

        if (!class_exists($platformDir . '\\' . ucfirst($platform) . 'Service' . '\\' . 'PostPublish' . ucfirst($platform) . 'Service')) {
            return false;
        }

        return [
            'class'    => $platformDir . '\\' . ucfirst($platform) . 'Service' . '\\' . 'PostPublish' . ucfirst($platform) . 'Service',
            'platform' => $platform,
            'details'  => \is_string($details) ? json_decode($details) : $details,
        ];
    }

    public function clipText($platform, $content, $link)
    {
        // Ensure $content is always a string
        $content = $content ?? '';

        $backSlashNCount = substr_count($content, "\n");
        $textLength = \strlen($content) - $backSlashNCount;

        $maxLength = $this->platformLimitations[$platform] ?? $textLength;

        if ($textLength > $maxLength) {
            return substr($content, 0, $maxLength);
        }

        return $content;
    }

    public function normalizePostData(array $data, bool $createOthers = false): array
    {
        // Remove media-related keys from allowed list (so they are not returned)
        $allowedKeys = ['content', 'media', 'link', 'comment'];

        $result = [
            'content' => $data['content'] ?? '',
            'media'   => [],
            'link'    => $data['link'] ?? '',
            'comment' => $data['comment'] ?? '',
        ];

        // Merge media
        if (!empty($data['media'])) {
            $result['media'] = \is_array($data['media']) ? $data['media'] : [$data['media']];
        }

        if (!empty($data['featureImage'])) {
            $result['media'][] = $data['featureImage'];
        }

        if (!empty($data['allImages'])) {
            $result['media'] = array_merge(
                $result['media'],
                \is_array($data['allImages']) ? $data['allImages'] : [$data['allImages']]
            );
        }

        if (!empty($data['video'])) {
            $result['media'][] = $data['video'];
        }

        // Remove empty / duplicate values
        $result['media'] = array_values(array_filter(array_unique($result['media'])));

        // Process other data
        foreach ($data as $key => $value) {
            if (!\in_array($key, $allowedKeys, true)
                && !\in_array($key, ['featureImage', 'allImages', 'video'], true)) {
                if ($createOthers) {
                    $result['others'][$key] = $value;
                } else {
                    $result[$key] = $value;
                }
            }
        }

        if ($createOthers && !isset($result['others'])) {
            $result['others'] = [];
        }

        return $result;
    }

    /**
     * Normalize image media URLs for platform compatibility.
     *
     * - Converts WebP to JPEG for platforms that do not accept WebP.
     * - Resizes overly large images to reduce API rejections.
     */
    protected function normalizeMediaForPlatform($media, $platform)
    {
        if (empty($media)) {
            return [];
        }

        $mediaItems = \is_array($media) ? $media : [$media];
        $normalizedMedia = [];

        foreach ($mediaItems as $mediaUrl) {
            if (!\is_string($mediaUrl) || empty($mediaUrl)) {
                continue;
            }

            $normalizedMedia[] = $this->normalizeSingleImageForPlatform($mediaUrl, (string) $platform);
        }

        return array_values(array_filter(array_unique($normalizedMedia)));
    }

    protected function normalizeSingleImageForPlatform(string $mediaUrl, string $platform): string
    {
        if (!wp_http_validate_url($mediaUrl)) {
            return $mediaUrl;
        }

        $attachmentId = attachment_url_to_postid($mediaUrl);
        if (empty($attachmentId)) {
            return $mediaUrl;
        }

        $filePath = get_attached_file($attachmentId);
        if (empty($filePath) || !file_exists($filePath)) {
            return $mediaUrl;
        }

        $editor = wp_get_image_editor($filePath);
        if (is_wp_error($editor)) {
            return $mediaUrl;
        }

        $imageSize = $editor->get_size();
        if (empty($imageSize['width']) || empty($imageSize['height'])) {
            return $mediaUrl;
        }

        [$maxWidth, $maxHeight] = $this->getMaxDimensionsForPlatform($platform);
        $mimeType = wp_get_image_mime($filePath);
        $targetMimeType = $this->getTargetMimeTypeForPlatform($mimeType, $platform);
        $targetExtension = $this->mimeTypeToExtension($targetMimeType);

        $currentExtension = strtolower((string) pathinfo($filePath, PATHINFO_EXTENSION));
        $requiresResize = $imageSize['width'] > $maxWidth || $imageSize['height'] > $maxHeight;
        $requiresFormatChange = !empty($targetExtension) && $currentExtension !== $targetExtension;

        if (!$requiresResize && !$requiresFormatChange) {
            return $mediaUrl;
        }

        $uploadDir = wp_get_upload_dir();
        if (empty($uploadDir['basedir']) || empty($uploadDir['baseurl'])) {
            return $mediaUrl;
        }

        $pathInfo = pathinfo($filePath);
        $targetPath = $pathInfo['dirname']
            . DIRECTORY_SEPARATOR
            . $pathInfo['filename']
            . '-bit-social-'
            . sanitize_key($platform)
            . '-' . $maxWidth . 'x' . $maxHeight
            . '.' . $targetExtension;

        if (!file_exists($targetPath)) {
            if ($requiresResize) {
                $editor->resize($maxWidth, $maxHeight, false);
            }

            $saveResult = $editor->save($targetPath, $targetMimeType);
            if (is_wp_error($saveResult) || empty($saveResult['path']) || !file_exists($saveResult['path'])) {
                return $mediaUrl;
            }

            $targetPath = $saveResult['path'];
        }

        return str_replace($uploadDir['basedir'], $uploadDir['baseurl'], $targetPath);
    }

    protected function getMaxDimensionsForPlatform(string $platform): array
    {
        $limits = [
            'facebook'              => [2000, 2000],
            'googleBusinessProfile' => [1200, 1200],
        ];

        return $limits[$platform] ?? [2000, 2000];
    }

    protected function getTargetMimeTypeForPlatform($mimeType, string $platform): string
    {
        $webpRestrictedPlatforms = ['facebook', 'googleBusinessProfile'];
        if (\in_array($platform, $webpRestrictedPlatforms, true) && $mimeType === 'image/webp') {
            return 'image/jpeg';
        }

        return $mimeType ?: 'image/jpeg';
    }

    protected function mimeTypeToExtension(string $mimeType): string
    {
        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];

        return $extensionMap[$mimeType] ?? 'jpg';
    }

    /**
     * Escape characters that could break Markdown/Telegram formatting.
     */
    protected function escapeSpecialCharacters(string $text): string
    {
        $search = ['\\', '|', '{', '}', '(', ')', '<', '>', '#', '*', '_', '~', '[', ']'];
        $replace = ['\\\\', '\|', '\{', '\}', '\(', '\)', '\<', '\>', '\#', '\*', '\_', '\~', '\[', '\]'];

        return str_replace($search, $replace, $text);
    }
}
