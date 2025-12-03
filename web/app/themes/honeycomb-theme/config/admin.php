<?php

declare(strict_types=1);

namespace SMHG;

add_filter(
    'determine_current_user',
    /**
     * Determine the current user based on authentication token from Authorization header.
     *
     * @param int|bool $userId User ID if one has been determined, false otherwise.
     * @return int|bool User ID if one has been determined, false otherwise.
     */
    static function ($userId) {
        if ($userId) {
            return $userId;
        }

        if (!$_SERVER['HTTP_AUTHORIZATION']) {
            return $userId;
        }

        $parts = explode(
            'Bearer ',
            trim($_SERVER['HTTP_AUTHORIZATION'])
        );

        if (count($parts) !== 2) {
            return $userId;
        }

        $user = getUserFromAccessToken($parts[1]);

        if ($user) {
            $userId = $user->ID;
        }

        return $userId;
    },
    20
);

add_filter(
    'preview_post_link',
    /**
     * Preview post links may incorrectly reference the
     * frontend app, so we need to modify these links so
     * that they reference WordPress instead. This allows
     * the preview flow to be correctly initiated.
     */
    static function (string $url) {
        return str_replace(APP_FRONTEND_URL, WP_HOME, $url);
    },
);

add_filter(
    'home_url',
    /**
     * Modify any URLs that are displayed to the user
     * in the admin so that they link to the frontend app.
     */
    static function (string $url, string $path, ?string $scheme): string {
        if ($scheme === 'rest') {
            return $url;
        }

        if (!is_admin() || is_preview()) {
            return $url;
        }

        if (!$path) {
            return APP_FRONTEND_URL;
        }

        return APP_FRONTEND_URL . $path;
    },
    10,
    3
);

/**
 * Enqueue any admin editor assets.
 */
add_action(
    'admin_head',
    static function (): void {
        wp_enqueue_style(
            'smhg-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            [],
            '1.0'
        );
    }
);
add_action(
    'enqueue_block_editor_assets',
    static function (): void {
        wp_enqueue_style(
            'smhg-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            [],
            '1.0'
        );
    }
);
