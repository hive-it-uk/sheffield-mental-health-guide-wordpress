<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        $postType = 'activity_location';

        register_post_type(
            $postType,
            [
                'public' => true,
                'menu_icon' => 'dashicons-location',
                'menu_position' => 6,
                'has_archive' => false,
                'rewrite' => [
                    'slug' => 'locations',
                ],
                'supports' => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'custom-fields',
                ],
                'show_ui' => true,
                'show_in_rest' => true,
                'labels' => [
                    'name' => 'Locations',
                    'add_new_item' => 'Add New Location',
                    'edit_item' => 'Edit Location',
                    'all_items' => 'Locations',
                    'singular_name' => 'Location',
                ],
                'show_in_menu' => 'edit.php?post_type=activity',
            ]
        );
    }
);
