<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'init',
    static function (): void {
        register_rest_field(
            'activity',
            'acfMeta',
            [
                'get_callback' => static function (array $post) {
                    $postId = $post['id'];

                    $startDate = formatACFDateToIso(get_post_meta($postId, ActivityMeta::START_DATE, true));
                    $endDate = formatACFDateToIso(get_post_meta($postId, ActivityMeta::END_DATE, true));

                    $weeklyInterval = get_post_meta($postId, ActivityMeta::WEEKLY_INTERVAL, true);
                    $monthlyWeek = get_post_meta($postId, ActivityMeta::MONTHLY_WEEK, true);

                    $serviceId = (int)get_post_meta($postId, ActivityMeta::ORGANISER_ORGANISING_SERVICE, true);

                    $callToAction = null;
                    if (get_post_meta($postId, ActivityMeta::CALL_TO_ACTION_LABEL, true)) {
                        $callToAction = [
                            'label' => get_post_meta($postId, ActivityMeta::CALL_TO_ACTION_LABEL, true),
                            'url' => get_post_meta($postId, ActivityMeta::CALL_TO_ACTION_URL, true),
                        ];
                    }

                    return [
                        'organiser' => [
                            'name' => get_post_meta($postId, ActivityMeta::ORGANISER_NAME, true),
                            'email' => get_post_meta($postId, ActivityMeta::ORGANISER_EMAIL, true),
                            'website' => get_post_meta($postId, ActivityMeta::ORGANISER_WEBSITE, true),
                            'phone' => get_post_meta($postId, ActivityMeta::ORGANISER_PHONE, true),
                            'servicePreview' => !$serviceId ? null : [
                                'name' => get_the_title($serviceId),
                                'id' => get_post_field('ID', $serviceId),
                                'slug' => get_post_field('post_name', $serviceId),
                                'excerpt' => get_the_excerpt($serviceId),
                                'imageUrl' => get_the_post_thumbnail_url($serviceId, ''),
                            ],
                        ],
                        'schedule' => [
                            'startDate' => $startDate,
                            'startTime' => get_post_meta($postId, ActivityMeta::START_TIME, true),
                            'endDate' => $endDate,
                            'endTime' => get_post_meta($postId, ActivityMeta::END_TIME, true),
                            'frequency' => get_post_meta($postId, ActivityMeta::FREQUENCY, true),
                            'weekday' => get_post_meta($postId, ActivityMeta::WEEKDAY, true),
                            'weeklyInterval' => $weeklyInterval !== '' ? (int)$weeklyInterval : null,
                            'monthlyWeek' => $monthlyWeek !== '' ? (int)$monthlyWeek : null,
                        ],
                        'howToAccess' => get_post_meta($postId, ActivityMeta::HOW_TO_ACCESS, true) ?: 'in-person',
                        'locationId' => (int)get_post_meta($postId, ActivityMeta::LOCATION, true),
                        'bookingRequired' => (bool)get_post_meta($postId, ActivityMeta::BOOKING_REQUIRED, true),
                        'ticketPrice' => get_post_meta($postId, ActivityMeta::TICKET_PRICE, true),
                        'callToAction' => $callToAction,
                    ];
                },
            ]
        );
        register_rest_field(
            'activity',
            'mainCategoryTerms',
            [
                'get_callback' => static function (array $post) {
                    $categories = get_the_terms($post->ID, Taxonomies::ACTIVITY_CATEGORY);

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
        add_filter(
            'rest_prepare_activity',
            static function (\WP_REST_Response $response) {
                if (isset($response->data['acfMeta']['locationId'])) {
                    $locationId = $response->data['acfMeta']['locationId'];

                    if (!$locationId) {
                        return $response;
                    }

                    $response->add_link(
                        'smhg:location',
                        rest_url("/wp/v2/activity_location/{$locationId}"),
                        [
                            'embeddable' => true,
                        ]
                    );
                }

                return $response;
            }
        );
        add_filter(
            'rest_prepare_activity',
            static function (\WP_REST_Response $response) {
                if (isset($response->data['acfMeta']['organiser']['serviceId'])) {
                    $serviceId = $response->data['acfMeta']['organiser']['serviceId'];

                    if (!$serviceId) {
                        return $response;
                    }

                    $response->add_link(
                        'smhg:service',
                        rest_url("/wp/v2/service/{$serviceId}"),
                        [
                            'embeddable' => true,
                        ]
                    );
                }

                return $response;
            }
        );
    }
);
