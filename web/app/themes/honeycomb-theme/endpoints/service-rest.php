<?php

declare(strict_types=1);

namespace SMHG;

add_filter(
    'rest_service_query',
    static function (array $args, \WP_REST_Request $request) {

        // If we're using the Service search bar and filters,
        // defer to Relevanssi to perform a search over indexed content.
        //
        // Otherwise defer to default WordPress search for searching by slug
        // etc.
        if (
            getQueryParam($request, 'searchContext') === 'service'
            && getQueryParam($request, 'search') !== null
        ) {
            $args['relevanssi'] = true;
        }

        if (getQueryParam($request, 'taxonomy_relation') === null) {
            $args['tax_query']['relation'] = 'AND';
        }

        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::CATEGORIES, Taxonomies::SERVICE_CATEGORY);
        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::TYPES, Taxonomies::SERVICE_TYPE);
        $args = addTaxonomyQueryTermIds($args, $request, SearchParams::AUDIENCES, Taxonomies::AUDIENCE);

        $args['meta_query']['relation'] = 'AND';

        // 'How to access' filtering
        $howToAccess = getQueryParam($request, SearchParams::HOW_TO_ACCESS, null);
        if (is_string($howToAccess) && in_array(strtolower($howToAccess), ['online', 'in-person'], true)) {
            // When HTA value not set on a service, it's treated as 'both'
            if ($howToAccess === 'online') {
                $args['meta_query'][] = [
                    'relation' => 'OR',
                    [
                        'key' => ServiceMeta::HOW_TO_ACCESS,
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => ServiceMeta::LOCATION_TYPE,
                        'compare' => '=',
                        'value' => 'online',
                    ],
                    [
                        'key' => ServiceMeta::LOCATION_TYPE,
                        'compare' => '=',
                        'value' => 'both',
                    ],
                ];
            }

            if ($howToAccess === 'in-person') {
                $args['meta_query'][] = [
                    'relation' => 'OR',
                    [
                        'key' => ServiceMeta::HOW_TO_ACCESS,
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => ServiceMeta::HOW_TO_ACCESS,
                        'compare' => '=',
                        'value' => $howToAccess,
                    ],
                    [
                        'key' => ServiceMeta::LOCATION_TYPE,
                        'compare' => '=',
                        'value' => 'both',
                    ],
                ];
            }
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
    'rest_service_collection_params',
    static function (array $params) {
        $params['orderby']['enum'][] = 'rand';
        return $params;
    }
);

add_action(
    'rest_api_init',
    static function (): void {
        register_rest_route(
            'smhg/v1',
            'service',
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'args' => array_merge(
                    formText('serviceName', 'Service name must be not be empty'),
                    formBool('isNewService', 'Is New Service must be not be empty', false),
                    formBool('hasNewActivities', 'Has New Activities must be not be empty', false),
                    formUrl('website', 'Website URL is not a valid URL', true),
                    formEmail('serviceEmail', 'Email address is not a valid email address'),
                    formText('servicePhone', 'Phone number must be not be empty'),
                    formUrl('facebook', 'Facebook link is not a valid URL', false),
                    formUrl('twitter', 'Twitter link is not a valid URL', false),
                    formUrl('instagram', 'Instagram link is not a valid URL', false),
                    formText('openingTimes', 'Opening times must be not be empty'),
                    formText('contactName', 'Contact name must be not be empty'),
                    formEmail('contactEmail', 'Contact email address is not a valid email address'),
                    formText('contactPhone', 'Contact phone number must be not be empty'),
                    formRecaptcha('add_a_service'),
                ),
                'permission_callback' => '__return_true',
                'callback' => static function (\WP_REST_Request $request): void {
                    $params = $request->get_json_params();

                    // This will be a plaintext email. We could improve this
                    // by rendering out a HTML email.
                    // TODO SMHG-99: Send HTML emails instead of plaintext
                    wp_mail(
                        'mhguide@sheffieldflourish.co.uk',
                        'Add a service submission',
                        "A person has used Sheffield Mental Health Guide's 'Add a service' form with the following details:\n\n" .
                        formatLabelAndField('serviceName', 'Service name', $params) .
                        formatLabelAndField('isNewService', 'Is new service', $params) .
                        formatLabelAndField('hasNewActivities', 'Has new activities', $params) .
                        formatLabelAndField('description', 'Service description', $params) .
                        formatLabelAndField('website', 'Website URL', $params) .
                        formatLabelAndField('serviceEmail', 'Email address', $params) .
                        formatLabelAndField('servicePhone', 'Phone number', $params) .
                        formatLabelAndField('facebook', 'Facebook link', $params) .
                        formatLabelAndField('twitter', 'Twitter link', $params) .
                        formatLabelAndField('instagram', 'Instagram link', $params) .
                        formatLabelAndField('openingTimes', 'Opening times', $params) .
                        formatLabelAndField('contactName', 'Contact name', $params) .
                        formatLabelAndField('contactEmail', 'Contact email address', $params) .
                        formatLabelAndField('contactPhone', 'Contact phone number', $params) .
                        formatLabelAndField('comments', 'Additional comments', $params)
                    );
                },
            ]
        );
    }
);
