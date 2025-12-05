<?php

declare(strict_types=1);

// Hide default WordPress Post type
add_filter(
    'register_post_type_args',
    static function ($args, $postType) {
        if ($postType === 'post') {
            $args['public'] = false;
            $args['show_ui'] = false;
            $args['show_in_menu'] = false;
            $args['show_in_admin_bar'] = false;
            $args['show_in_nav_menus'] = false;
            $args['can_export'] = false;
            $args['has_archive'] = false;
            $args['exclude_from_search'] = true;
            $args['publicly_queryable'] = false;
            $args['show_in_rest'] = false;
        }
        return $args;
    },
    0,
    2
);

// Remove comments
add_action(
    'admin_init',
    static function (): void {
        remove_menu_page('edit-comments.php');
    }
);
