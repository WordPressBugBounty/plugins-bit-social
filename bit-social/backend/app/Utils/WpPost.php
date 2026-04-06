<?php

namespace BitApps\Social\Utils;

trait WpPost
{
    public function getPost($filter)
    {
        $filter['numberposts'] = 1;
        $filter['lang'] = '';
        $post = get_posts($filter);

        if ($post) {
            return [
                'id'   => $post[0]->ID,
                'name' => $post[0]->post_title
            ];
        }

        return [];
    }

    public function getPaginationPosts($filter)
    {
        $page = 1;
        $batchSize = 500;
        $allPosts = [];
        $baseFilter = $this->preparePostFilter($filter);

        do {
            $queryFilter = array_merge($baseFilter, [
                'paged'                  => $page,
                'posts_per_page'         => $batchSize,
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            ]);

            $posts = get_posts($queryFilter);

            if (empty($posts)) {
                break;
            }

            foreach ($posts as $post) {
                $allPosts[] = [
                    'id'   => $post->ID,
                    'name' => $post->post_title,
                ];
            }

            ++$page;
        } while (\count($posts) === $batchSize);

        return $allPosts;
    }

    public function getPostsCount($filter)
    {
        $page = 1;
        $batchSize = 500;
        $count = 0;
        $baseFilter = $this->preparePostFilter($filter);

        do {
            $queryFilter = array_merge($baseFilter, [
                'fields'                 => 'ids',
                'paged'                  => $page,
                'posts_per_page'         => $batchSize,
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            ]);

            $postIds = get_posts($queryFilter);
            $batchCount = \count($postIds);
            $count += $batchCount;
            ++$page;
        } while ($batchCount === $batchSize);

        return $count;
    }

    public function getTerms($data)
    {
        $options = [
            'hide_empty' => false,
            'orderby'    => 'id',
            'order'      => 'DESC'
        ];
        $options = array_merge($options, $data);
        $terms = get_terms($options);

        return array_map(function ($term) {
            return [
                'id'   => $term->term_id,
                'name' => $term->name,
            ];
        }, $terms);
    }

    private function preparePostFilter($filter)
    {
        $filter = \is_array($filter) ? $filter : [];
        $filter['lang'] = '';

        unset($filter['numberposts']);

        return array_filter($filter, function ($value) {
            if (\is_array($value)) {
                return !empty($value);
            }

            return $value !== '' && $value !== null;
        });
    }
}
