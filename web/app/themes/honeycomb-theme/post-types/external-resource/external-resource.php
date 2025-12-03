<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        register_post_type(
            'external_resource',
            [
                'public' => true,
                'menu_icon' => 'dashicons-media-document',
                'menu_position' => 9,
                'has_archive' => true,
                'rewrite' => [
                    'slug' => 'externalResource',
                ],
                'supports' => [
                    'title',
                    'excerpt',
                    'custom-fields',
                ],
                'show_in_rest' => true,
                'labels' => [
                    'name' => 'External Resources',
                    'add_new_item' => 'Add New External Resources',
                    'edit_item' => 'Edit External Resources',
                    'all_items' => 'All External Resources',
                    'singular_name' => 'External Resource',
                ],
                'taxonomies' => ['service_category'],
            ]
        );
    }
);
