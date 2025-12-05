<?php

declare(strict_types=1);

namespace SMHG;

function isGeolocationSearch(): bool
{
    if (!isset($_GET['searchContext'])) {
        return false;
    }

    $postType = $_GET['searchContext'];

    return ($postType === 'activity' || $postType === 'service') &&
        isset($_GET[SearchParams::DISTANCE_LAT]) &&
        is_numeric($_GET[SearchParams::DISTANCE_LAT]) &&
        isset($_GET[SearchParams::DISTANCE_LONG]) &&
        is_numeric($_GET[SearchParams::DISTANCE_LONG]) &&
        isset($_GET[SearchParams::DISTANCE_RANGE]) &&
        is_numeric($_GET[SearchParams::DISTANCE_RANGE]);
}

add_filter(
    'relevanssi_join',
    static function ($join) {

        global $wpdb;

        if (!isGeolocationSearch()) {
            return $join;
        }

        $postType = $_GET['searchContext'];

        if ($postType === 'service') {
            $latJoin = $wpdb->prepare(
                "INNER JOIN {$wpdb->postmeta} AS postmeta_lat
                    ON relevanssi.doc = postmeta_lat.post_id
                    AND postmeta_lat.meta_key = %s
                    AND postmeta_lat.meta_value IS NOT NULL
                    AND postmeta_lat.meta_value != '' ",
                [LocationMeta::LATITUDE]
            );
            $longJoin = $wpdb->prepare(
                "INNER JOIN {$wpdb->postmeta} AS postmeta_long 
                    ON relevanssi.doc = postmeta_long.post_id 
                    AND postmeta_long.meta_key = %s 
                    AND postmeta_long.meta_value IS NOT NULL 
                    AND postmeta_long.meta_value != '' ",
                [LocationMeta::LONGITUDE]
            );

            $join .= $latJoin . $longJoin;
        }
        if ($postType === 'activity') {
            $locationJoin = $wpdb->prepare(
                "INNER JOIN {$wpdb->postmeta} AS postmeta_location 
                    ON relevanssi.doc = postmeta_location.post_id 
                    AND postmeta_location.meta_key = %s 
                    AND postmeta_location.meta_value IS NOT NULL 
                    AND postmeta_location.meta_value != '' ",
                [ActivityMeta::LOCATION]
            );
            $latJoin = $wpdb->prepare(
                "INNER JOIN {$wpdb->postmeta} AS postmeta_lat 
                    ON postmeta_location.meta_value = postmeta_lat.post_id 
                    AND postmeta_lat.meta_key = %s 
                    AND postmeta_lat.meta_value IS NOT NULL 
                    AND postmeta_lat.meta_value != '' ",
                [LocationMeta::LATITUDE]
            );
            $longJoin = $wpdb->prepare(
                "INNER JOIN {$wpdb->postmeta} AS postmeta_long 
                    ON postmeta_location.meta_value = postmeta_long.post_id 
                    AND postmeta_long.meta_key = %s 
                    AND postmeta_long.meta_value IS NOT NULL 
                    AND postmeta_long.meta_value != '' ",
                [LocationMeta::LONGITUDE]
            );

            $join .= $locationJoin . $latJoin . $longJoin;
        }

        return $join;
    },
    20,
    1
);

add_filter(
    'relevanssi_where',
    static function ($query) {

        global $wpdb;

        if (!isGeolocationSearch()) {
            return $query;
        }

        $lat = $_GET[SearchParams::DISTANCE_LAT];
        $lng = $_GET[SearchParams::DISTANCE_LONG];
        $proximity = (float)$_GET[SearchParams::DISTANCE_RANGE] * 1000; // from km to metres;

        $distanceWhere = $wpdb->prepare(
            "
            ST_distance_sphere(
                ST_Geomfromtext('POINT(%f %f)',4326),
                ST_GeomFromText(CONCAT('POINT(', postmeta_lat.meta_value, ' ', postmeta_long.meta_value, ')'), 4326)
            ) <= %d",
            [$lat, $lng, $proximity]
        );

        return $query . ' AND ' . $distanceWhere;
    },
    20,
    1
);

add_action(
    'acf/save_post',
    static function ($postId): void {
        // Store map meta as queryable lat/long metadata.

        $postType = get_post_type($postId);

        if (($postType !== 'activity_location' && $postType !== 'service') || get_post_status($postId) !== 'publish') {
            return;
        }

        $mapLocation = get_field(LocationMeta::MAP, $postId);
        if (!isset($mapLocation) || !isset($mapLocation[SearchParams::DISTANCE_LAT]) || !isset($mapLocation[SearchParams::DISTANCE_LONG])) {
            return;
        }

        update_post_meta($postId, LocationMeta::LATITUDE, $mapLocation[SearchParams::DISTANCE_LAT]);
        update_post_meta($postId, LocationMeta::LONGITUDE, $mapLocation[SearchParams::DISTANCE_LONG]);
    }
);
