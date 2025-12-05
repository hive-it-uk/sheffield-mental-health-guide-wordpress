<?php

declare(strict_types=1);

add_action(
    'init',
    static function (): void {
        register_post_type(
            'activity',
            [
                'public'      => true,
                'menu_icon'   => 'dashicons-art',
                'menu_position'   => 6,
                'has_archive' => true,
                'rewrite'     => [
                    'slug' => 'activities',
                ],
                'supports'    => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'custom-fields',
                ],
                'show_in_rest' => true,
                'labels'      => [
                    'name'          => 'Activities',
                    'add_new_item'  => 'Add New Activity',
                    'edit_item'     => 'Edit Activity',
                    'all_items'     => 'All Activities',
                    'singular_name' => 'Activity',
                ],
                'taxonomies' => [ 'post_tag', 'age', 'activity_type', 'activity_category' ],
            ]
        );

        // Setup smhg_on_save_activity_post hook to generate activity_instances.
        add_action(
            'save_post',
            static function ($postId): void {
                if (get_post_type($postId) !== 'activity' || get_post_status($postId) !== 'publish') {
                    return;
                }

                SMHG\limitActivityEndDateFromStartDate($postId);
                SMHG\createActivityInstances($postId);
            }
        );
        add_action(
            'delete_post',
            static function ($postId): void {
                if (get_post_type($postId) !== 'activity') {
                    return;
                }

                SMHG\removeCurrentActivityInstances($postId);
            }
        );
    }
);
