<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'init',
    static function (): void {
        register_rest_field(
            'service',
            'acfMeta',
            [
                'get_callback' => static function (array $post) {
                    $postId = $post['id'];

                    $map = get_post_meta($postId, ServiceMeta::MAP, true);
                    $serviceLocationType = get_post_meta($postId, ServiceMeta::LOCATION_TYPE, true);
                    $howToAccess = get_post_meta($postId, ServiceMeta::HOW_TO_ACCESS, true) ?: 'both';

                    $callToAction = null;
                    if (get_post_meta($postId, ServiceMeta::CALL_TO_ACTION_LABEL, true)) {
                        $callToAction = [
                            'label' => get_post_meta($postId, ServiceMeta::CALL_TO_ACTION_LABEL, true),
                            'url' => get_post_meta($postId, ServiceMeta::CALL_TO_ACTION_URL, true),
                        ];
                    }

                    return [
                        'email' => get_post_meta($postId, ServiceMeta::EMAIL, true),
                        'phone' => get_post_meta($postId, ServiceMeta::PHONE, true),
                        'website' => get_post_meta($postId, ServiceMeta::WEBSITE, true),
                        'facebook' => get_post_meta($postId, ServiceMeta::FACEBOOK, true),
                        'instagram' => get_post_meta($postId, ServiceMeta::INSTAGRAM, true),
                        'twitter' => get_post_meta($postId, ServiceMeta::TWITTER, true),
                        'tiktok' => get_post_meta($postId, ServiceMeta::TIKTOK, true),
                        'openingTimes' => get_field(ServiceMeta::OPENING_TIMES),
                        'howToAccess' => $serviceLocationType === 'online' ? 'online' : $howToAccess,
                        'location' => [
                            'type' => $serviceLocationType,
                            'address1' => get_post_meta($postId, ServiceMeta::ADDRESS_1, true),
                            'address2' => get_post_meta($postId, ServiceMeta::ADDRESS_2, true),
                            'address3' => get_post_meta($postId, ServiceMeta::ADDRESS_3, true),
                            'city' => get_post_meta($postId, ServiceMeta::CITY, true),
                            'postcode' => get_post_meta($postId, ServiceMeta::POSTCODE, true),
                            'info' => get_field(ServiceMeta::INFO),
                            'map' => $map ?: null,
                        ],
                        'callToAction' => $callToAction,
                    ];
                },
            ]
        );
        register_rest_field(
            'service',
            'mainCategoryTerms',
            [
                'get_callback' => static function (array $post) {
                    $categories = get_the_terms($post->ID, Taxonomies::SERVICE_CATEGORY);

                    if (!$categories) {
                        return [];
                    }

                    return array_map(
                        static function ($category) {
                            return [
                            'id' => $category->term_id,
                            'name' => $category->name,
                            ];
                        },
                        $categories
                    );
                },
            ]
        );
    }
);
