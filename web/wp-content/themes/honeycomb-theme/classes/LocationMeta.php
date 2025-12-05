<?php

declare(strict_types=1);

namespace SMHG;

/**
 * class LocationMeta houses location meta keys.
 */
abstract class LocationMeta
{
    public const ADDRESS_1 = 'location_address_line_1';
    public const ADDRESS_2 = 'location_address_line_2';
    public const ADDRESS_3 = 'location_address_line_3';
    public const CITY = 'location_address_city';
    public const POSTCODE = 'location_address_postcode';
    public const WEBSITE = 'location_address_website';
    public const INFO = 'location_address_more_info';
    public const MAP = 'location_map_location';

    //Geolocation meta
    public const LATITUDE = 'location_latitude';
    public const LONGITUDE = 'location_longitude';
}
