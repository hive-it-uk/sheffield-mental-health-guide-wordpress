<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        register_post_type(
            'team_member',
            [
                'public' => true,
                'menu_icon' => 'dashicons-groups',
                'menu_position' => 7,
                'has_archive' => true,
                'rewrite' => [
                    'slug' => 'about-us/team',
                ],
                'supports' => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                ],
                'show_in_rest' => true,
                'labels' => [
                    'name' => 'Team',
                    'add_new_item' => 'Add New Team Member',
                    'edit_item' => 'Edit Team Member',
                    'all_items' => 'All Team',
                    'singular_name' => 'Team Member',
                ],
            ],
        );
    }
);
