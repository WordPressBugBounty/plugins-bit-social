<?php

namespace BitApps\Social\Utils;

trait PostInfo
{
    public function getFeaturedImageUrl($postId)
    {
        if (has_post_thumbnail($postId)) {
            return get_the_post_thumbnail_url($postId, 'full');
        }
    }

    public function getContentShort($content, $length)
    {
        if (\strlen($content) > $length) {
            return substr($content, 0, $length);
        }

        return $content;
    }

    public static function getAllImages($postId)
    {
        $post = get_post($postId);
        $post_content = $post->post_content;

        $pattern = '/<img.*?src="([^"]*)"/';

        $matches = [];

        preg_match_all($pattern, $post_content, $matches);

        return $matches[1];
    }

    // public function getAllImages($postId)
    // {
    //     $allImagesUrl = [];
    //     $featuredImageUrl = $this->getFeaturedImageUrl($postId);

    //     $allImages = get_attached_media('image', $postId);

    //     foreach ($allImages as $image) {
    //         $imageUrl = $image->guid;
    //         if ($imageUrl != $featuredImageUrl) {
    //             if (! \in_array($imageUrl, $allImagesUrl)) {
    //                 $allImagesUrl[] = $imageUrl;
    //             }
    //         }
    //     }

    //     return $allImagesUrl;
    // }

    public function getVideo($post_id)
    {
        $post_content = get_post($post_id)->post_content;

        preg_match('/<figure.*src=\"(.*)\".*><\/figure>/iU', $post_content, $matches);

        return !empty($matches[1]) ? $matches[1] : '';
    }
}
