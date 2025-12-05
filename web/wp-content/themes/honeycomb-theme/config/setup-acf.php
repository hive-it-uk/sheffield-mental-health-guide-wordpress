<?php

declare(strict_types=1);

add_filter(
    'acf/settings/rest_api_format',
    static function () {
        // Makes `acf` fields in REST API return a more useable format.
        return 'standard';
    }
);

if (defined('APP_GOOGLE_MAPS_API_KEY')) {
    add_filter(
        'acf/fields/google_map/api',
        static function ($api) {
            $api['key'] = APP_GOOGLE_MAPS_API_KEY;
            return $api;
        }
    );
}
