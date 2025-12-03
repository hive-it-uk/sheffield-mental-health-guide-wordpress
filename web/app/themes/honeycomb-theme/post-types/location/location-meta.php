<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'init',
    static function (): void {
        register_rest_field(
            'activity_location',
            'acfMeta',
            [
                'get_callback' => static function (array $post) {
                    $postId = $post['id'];

                    $map = get_post_meta($postId, LocationMeta::MAP, true);

                    return [
                        'address1' => get_post_meta($postId, LocationMeta::ADDRESS_1, true),
                        'address2' => get_post_meta($postId, LocationMeta::ADDRESS_2, true),
                        'address3' => get_post_meta($postId, LocationMeta::ADDRESS_3, true),
                        'city' => get_post_meta($postId, LocationMeta::CITY, true),
                        'postcode' => get_post_meta($postId, LocationMeta::POSTCODE, true),
                        'info' => get_post_meta($postId, LocationMeta::INFO, true),
                        'map' => $map !== '' ? $map : null,
                    ];
                },
            ]
        );
    }
);
