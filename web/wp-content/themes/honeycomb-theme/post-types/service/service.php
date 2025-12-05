<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        register_post_type(
            'service',
            [
                'public' => true,
                'menu_icon' => 'dashicons-coffee',
                'menu_position' => 5,
                'has_archive' => true,
                'rewrite' => [
                    'slug' => 'services',
                ],
                'supports' => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'custom-fields',
                ],
                'show_in_rest' => true,
                'labels' => [
                    'name' => 'Services',
                    'add_new_item' => 'Add New Service',
                    'edit_item' => 'Edit Service',
                    'all_items' => 'All Services',
                    'singular_name' => 'Service',
                ],
                'taxonomies' => ['post_tag', 'age', 'service_type', 'service_category'],
            ]
        );
    }
);
