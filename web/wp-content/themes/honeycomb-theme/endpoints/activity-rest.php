<?php

declare(strict_types=1);

namespace SMHG;

add_filter(
    'rest_activity_query',
    static function (array $args, \WP_REST_Request $request) {

        // If we're using the Activity search bar and filters,
        // defer to Relevanssi to perform a search over indexed content.
        //
        // Otherwise defer to default WordPress search for searching by slug
        // etc.
        if (
            getQueryParam($request, 'searchContext') === 'activity'
            && getQueryParam($request, 'search') !== null
        ) {
            $args['relevanssi'] = true;
        }

        if (getQueryParam($request, 'taxonomy_relation') === null) {
            $args['tax_query']['relation'] = 'AND';
        }

        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::CATEGORIES, Taxonomies::ACTIVITY_CATEGORY);
        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::TYPES, Taxonomies::ACTIVITY_TYPE);
        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::AUDIENCES, Taxonomies::AUDIENCE);

        $args['meta_query']['relation'] = 'AND';
        $startDate = getQueryParam($request, SearchParams::START_DATE);
        $endDate = getQueryParam($request, SearchParams::END_DATE);
        $timePeriod = getQueryParam($request, SearchParams::TIME_PERIOD);

        if (isset($timePeriod) && in_array($timePeriod, ['today', 'week'], true)) {
            // if $timePeriod is set, and equals 'today' or 'week'
            $args['meta_query'][] = [
                'key' => ActivityMeta::ACTIVITY_INSTANCES,
                'compare' => 'BETWEEN',
                'value' => [
                    $timePeriod === 'today' ? gmdate('Y-m-d') : gmdate('Y-m-d', strtotime('monday this week')),
                    $timePeriod === 'today' ? gmdate('Y-m-d') : gmdate('Y-m-d', strtotime('sunday this week')),
                ],
                'type' => 'DATE',
            ];
        } else {
            // fallback to startDate and endDate filtering
            if ((isValidDate($startDate, 'Y-m-d') || isValidDate($endDate, 'Y-m-d'))) {
                // set meta_query date search, if a date has been provided
                if (isValidDate($startDate, 'Y-m-d') && isValidDate($endDate, 'Y-m-d')) {
                    // if both dates provided
                    $args['meta_query'][] = [
                        'key' => ActivityMeta::ACTIVITY_INSTANCES,
                        'compare' => 'BETWEEN',
                        'value' => [$startDate, $endDate],
                        'type' => 'DATE',
                    ];
                }
                if (isValidDate($startDate, 'Y-m-d') && !isValidDate($endDate, 'Y-m-d')) {
                    // if only startDate provided
                    $args['meta_query'][] = [
                        [
                            'key' => ActivityMeta::ACTIVITY_INSTANCES,
                            'compare' => '>=',
                            'value' => $startDate,
                            'type' => 'DATE',
                        ],
                    ];
                }
                if (!isValidDate($startDate, 'Y-m-d') && isValidDate($endDate, 'Y-m-d')) {
                    // if only endDate provided
                    $args['meta_query'][] = [
                        [
                            'key' => ActivityMeta::ACTIVITY_INSTANCES,
                            'compare' => '<=',
                            'value' => $endDate,
                            'type' => 'DATE',
                        ],
                    ];
                }
            }
        }

        // 'How to access' filtering
        $howToAccess = getQueryParam($request, SearchParams::HOW_TO_ACCESS, null);
        if (is_string($howToAccess) && in_array(strtolower($howToAccess), ['online', 'in-person'], true)) {
            $args['meta_query'][] = $howToAccess === 'in-person' ? [
                    'relation' => 'OR',
                    [
                        'key' => ActivityMeta::HOW_TO_ACCESS,
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => ActivityMeta::HOW_TO_ACCESS,
                        'compare' => '=',
                        'value' => 'in-person',
                    ],
                    [
                        'key' => ActivityMeta::HOW_TO_ACCESS,
                        'compare' => '=',
                        'value' => 'both',
                    ],
                ] : [
                    'relation' => 'OR',
                    [
                        'key' => ActivityMeta::HOW_TO_ACCESS,
                        'compare' => '=',
                        'value' => 'online',
                    ],
                    [
                        'key' => ActivityMeta::HOW_TO_ACCESS,
                        'compare' => '=',
                        'value' => 'both',
                    ],
                ];
        }

        // Organising service filtering
        $serviceId = getQueryParam($request, SearchParams::SERVICE_ID, null);
        if (is_string($serviceId)) {
            $args['meta_query'][] = [
                'key' => ActivityMeta::ORGANISER_ORGANISING_SERVICE,
                'compare' => '=',
                'value' => $serviceId,
            ];
        }

        if (getQueryParam($request, 'search')) {
            $args['orderby'] = 'relevance';
            $args['order'] = 'DESC';
        }

        return $args;
    },
    10,
    2
);

add_filter(
    'rest_activity_collection_params',
    static function (array $params) {
        $params['orderby']['enum'][] = 'rand';
        return $params;
    }
);
