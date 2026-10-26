<?php

namespace BitApps\Social\Utils;

use BitApps\Social\Config;
use BitApps\Social\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\Social\Deps\BitApps\WPKit\Http\Response;
use BitApps\Social\HTTP\Controllers\AccountController;
use BitApps\Social\Model\Schedule;
use finfo;

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
     * Whether the current user may publish through every account referenced
     * by a share-now/schedule request ({accountIds: [], groupIds: []}). Pro
     * hooks the filter to enforce per-account role permissions server-side —
     * the pickers already hide restricted accounts, but the save endpoints
     * must not trust the submitted ids. Free allows everything.
     *
     * @param mixed $accountsConfig
     *
     * @return bool
     */
    public function canUseRequestedAccounts($accountsConfig)
    {
        return (bool) Hooks::applyFilter(Config::withPrefix('can_use_accounts'), true, $accountsConfig);
    }

    public function accountsNotPermittedResponse()
    {
        return Response::error(__('You are not permitted to publish through one or more of the selected accounts.', 'bit-social'));
    }

    /**
     * Whether the current user may manage (delete / pause / enable) every given
     * schedule or share-now item — i.e. their role may use all the accounts each
     * item posts through. Mirrors the create/update gate: pro's can_use_accounts
     * filter enforces per-account permissions, while free and admins pass. An
     * item that references no restricted account stays manageable by anyone with
     * plugin access.
     *
     * @param mixed $scheduleIds
     *
     * @return bool
     */
    public function canManageSchedules($scheduleIds)
    {
        if (empty($scheduleIds)) {
            return true;
        }

        $schedules = Schedule::whereIn('id', (array) $scheduleIds)->get(['id', 'config']);

        foreach ((array) $schedules as $schedule) {
            $accountsConfig = $schedule->config['accounts'] ?? [];

            if (!$this->canUseRequestedAccounts($accountsConfig)) {
                return false;
            }
        }

        return true;
    }

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
            $trimMessage = $templateOptions[$template->platform]['trimMessage'] ?? true;
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
            $media = $this->normalizeMediaForPlatform($media, $template->platform, $template);
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
     *
     * @param mixed       $media
     * @param mixed       $platform
     * @param null|object $template carries the platform's image layout choice
     */
    protected function normalizeMediaForPlatform($media, $platform, $template = null)
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

            $normalizedMedia[] = $this->normalizeSingleImageForPlatform($mediaUrl, (string) $platform, $template);
        }

        return array_values(array_filter(array_unique($normalizedMedia)));
    }

    /**
     * @param null|object $template
     */
    protected function normalizeSingleImageForPlatform(string $mediaUrl, string $platform, $template = null): string
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

        // An image outside the platform's ratio range is either center-cropped
        // into it or padded out to it, whichever layout the site chose.
        $layout = $this->getImageLayoutForPlatform($platform, $template);
        $outOfRangeCrop = $this->getAspectRatioCrop((int) $imageSize['width'], (int) $imageSize['height'], $platform);
        $crop = $layout['mode'] === 'crop' ? $outOfRangeCrop : null;
        $padColor = $layout['mode'] === 'fit' && $outOfRangeCrop ? $layout['background'] : null;

        $finalWidth = $crop ? $crop['width'] : $imageSize['width'];
        $finalHeight = $crop ? $crop['height'] : $imageSize['height'];

        $currentExtension = strtolower((string) pathinfo($filePath, PATHINFO_EXTENSION));
        $requiresResize = $finalWidth > $maxWidth || $finalHeight > $maxHeight;
        $requiresFormatChange = !empty($targetExtension) && $currentExtension !== $targetExtension;

        if (!$crop && !$padColor && !$requiresResize && !$requiresFormatChange) {
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
            . ($crop ? '-crop' : '')
            . ($padColor ? '-fit-' . ltrim($padColor, '#') : '')
            . '.' . $targetExtension;

        if (!file_exists($targetPath)) {
            if ($crop && is_wp_error($editor->crop($crop['x'], $crop['y'], $crop['width'], $crop['height']))) {
                return $mediaUrl;
            }

            if ($requiresResize) {
                $editor->resize($maxWidth, $maxHeight, false);
            }

            $saveResult = $editor->save($targetPath, $targetMimeType);
            if (is_wp_error($saveResult) || empty($saveResult['path']) || !file_exists($saveResult['path'])) {
                return $mediaUrl;
            }

            $targetPath = $saveResult['path'];

            if ($padColor && !$this->padToAspectRatio($targetPath, $platform, $padColor)) {
                wp_delete_file($targetPath);

                return $mediaUrl;
            }
        }

        return str_replace($uploadDir['basedir'], $uploadDir['baseurl'], $targetPath);
    }

    /**
     * How an image outside the platform's ratio range is brought into it:
     * "crop" (center-crop) or "fit" (pad with a background color).
     *
     * Only Instagram has a choice, set per template (feedImageLayout and
     * backgroundColor). A template saved before the choice existed gets the
     * defaults in SocialTemplateController::$defaultTemplateSettings.
     *
     * @param null|object $template
     */
    protected function getImageLayoutForPlatform(string $platform, $template = null): array
    {
        $crop = ['mode' => 'crop', 'background' => '#000000'];

        if ($platform !== 'instagram') {
            return $crop;
        }

        $mode = isset($template->feedImageLayout) ? $template->feedImageLayout : 'fit';
        $background = sanitize_hex_color(isset($template->backgroundColor) ? (string) $template->backgroundColor : '') ?: '#000000';

        // Anything but an explicit "crop" is the default fit. Padding draws on a
        // GD canvas; without GD, cropping is the only way into range at all.
        if ($mode !== 'crop' && \function_exists('imagecreatetruecolor')) {
            return ['mode' => 'fit', 'background' => $background];
        }

        return $crop;
    }

    /**
     * Places the saved image, centered, on a background-colored canvas at the
     * nearest ratio the platform accepts, so the whole picture stays visible.
     *
     * Writes JPEG: Instagram, the only platform with a fit layout, takes
     * nothing else.
     */
    protected function padToAspectRatio(string $path, string $platform, string $background): bool
    {
        $range = $this->getAspectRatioRangeForPlatform($platform);
        $size = getimagesize($path);
        if ($range === null || empty($size[0]) || empty($size[1])) {
            return false;
        }

        [$minRatio, $maxRatio] = $range;
        $width = (int) $size[0];
        $height = (int) $size[1];
        $ratio = $width / $height;

        // Ceil the grown side so the canvas never lands just outside the range.
        $canvasWidth = $ratio < $minRatio ? (int) ceil($height * $minRatio) : $width;
        $canvasHeight = $ratio > $maxRatio ? (int) ceil($width / $maxRatio) : $height;

        if ($canvasWidth === $width && $canvasHeight === $height) {
            return true;
        }

        $source = imagecreatefromstring((string) file_get_contents($path));
        if (!$source) {
            return false;
        }

        $hex = ltrim($background, '#');
        if (\strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        [$red, $green, $blue] = array_map('hexdec', str_split($hex, 2));

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, $red, $green, $blue));
        imagecopy(
            $canvas,
            $source,
            (int) floor(($canvasWidth - $width) / 2),
            (int) floor(($canvasHeight - $height) / 2),
            0,
            0,
            $width,
            $height
        );

        $saved = imagejpeg($canvas, $path, 90);

        imagedestroy($source);
        imagedestroy($canvas);

        return $saved;
    }

    protected function getMaxDimensionsForPlatform(string $platform): array
    {
        $limits = [
            'facebook'              => [2000, 2000],
            'googleBusinessProfile' => [1200, 1200],
            'instagram'             => [1440, 1440],
        ];

        return $limits[$platform] ?? [2000, 2000];
    }

    /**
     * The width / height range a platform accepts, or null when any ratio goes.
     *
     * Instagram rejects feed images outside 4:5 to 1.91:1 (error 2207009).
     */
    protected function getAspectRatioRangeForPlatform(string $platform): ?array
    {
        $ranges = [
            'instagram' => [0.8, 1.91],
        ];

        return $ranges[$platform] ?? null;
    }

    /**
     * The centered crop that brings an image into the platform's ratio range.
     *
     * Floors the kept side so the result never lands just outside the range.
     *
     * @return null|array{x: int, y: int, width: int, height: int} null when no crop is needed
     */
    protected function getAspectRatioCrop(int $width, int $height, string $platform): ?array
    {
        $range = $this->getAspectRatioRangeForPlatform($platform);
        if ($range === null || $width < 1 || $height < 1) {
            return null;
        }

        [$minRatio, $maxRatio] = $range;
        $ratio = $width / $height;

        if ($ratio < $minRatio) {
            $newHeight = (int) floor($width / $minRatio);

            return ['x' => 0, 'y' => (int) floor(($height - $newHeight) / 2), 'width' => $width, 'height' => $newHeight];
        }

        if ($ratio > $maxRatio) {
            $newWidth = (int) floor($height * $maxRatio);

            return ['x' => (int) floor(($width - $newWidth) / 2), 'y' => 0, 'width' => $newWidth, 'height' => $height];
        }

        return null;
    }

    protected function getTargetMimeTypeForPlatform($mimeType, string $platform): string
    {
        // Instagram's image_url only accepts JPEG; anything else fails with an
        // unreadable aspect ratio.
        if ($platform === 'instagram') {
            return 'image/jpeg';
        }

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
     * Resolves a media MIME type from its raw bytes.
     *
     * fileinfo is optional in PHP and disabled on a number of shared hosts, so
     * this falls back to the core image reader and finally to the file name.
     *
     * @param string $fileContent raw file bytes
     * @param string $fileName    used for the extension based fallback
     *
     * @return string empty when the type cannot be resolved
     */
    protected function detectMimeType($fileContent, $fileName = '')
    {
        if (!empty($fileContent) && class_exists('finfo')) {
            $mimeType = (new finfo(FILEINFO_MIME_TYPE))->buffer($fileContent);

            if (!empty($mimeType)) {
                return $mimeType;
            }
        }

        if (!empty($fileContent)) {
            $imageInfo = @getimagesizefromstring($fileContent);

            if (!empty($imageInfo['mime'])) {
                return $imageInfo['mime'];
            }
        }

        if (!empty($fileName)) {
            $fileType = wp_check_filetype($fileName);

            if (!empty($fileType['type'])) {
                return $fileType['type'];
            }
        }

        return '';
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
