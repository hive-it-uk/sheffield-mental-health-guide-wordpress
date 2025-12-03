<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        register_post_type(
            'resource',
            [
                'public' => true,
                'menu_icon' => 'dashicons-media-document',
                'menu_position' => 8,
                'has_archive' => true,
                'rewrite' => [
                    'slug' => 'resources',
                ],
                'supports' => [
                    'title',
                    'editor',
                    'excerpt',
                    'custom-fields',
                ],
                'show_in_rest' => true,
                'labels' => [
                    'name' => 'Resource Pages',
                    'add_new_item' => 'Add New Resource Page',
                    'edit_item' => 'Edit Resource Pages',
                    'all_items' => 'All Resource Pages',
                    'singular_name' => 'Resource Page',
                ],
                'taxonomies' => ['service_category'],
            ]
        );
    }
);
