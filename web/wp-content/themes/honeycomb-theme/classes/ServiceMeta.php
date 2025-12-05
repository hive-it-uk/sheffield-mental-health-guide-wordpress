<?php

declare(strict_types=1);

namespace SMHG;

/**
 * class ServiceMeta houses location meta keys.
 */
abstract class ServiceMeta
{
    public const EMAIL = 'service_email';
    public const PHONE = 'service_phone';
    public const WEBSITE = 'service_website';
    public const FACEBOOK = 'service_facebook';
    public const INSTAGRAM = 'service_instagram';
    public const TWITTER = 'service_twitter';
    public const TIKTOK = 'service_tiktok';
    public const OPENING_TIMES = 'service_opening_times';
    public const HOW_TO_ACCESS = 'how_to_access';
    public const CALL_TO_ACTION_LABEL = 'call_to_action_label';
    public const CALL_TO_ACTION_URL = 'call_to_action_url';

    public const LOCATION_TYPE = 'service_location_type';

    public const ADDRESS_1 = LocationMeta::ADDRESS_1;
    public const ADDRESS_2 = LocationMeta::ADDRESS_2;
    public const ADDRESS_3 = LocationMeta::ADDRESS_3;
    public const CITY = LocationMeta::CITY;
    public const POSTCODE = LocationMeta::POSTCODE;
    public const INFO = LocationMeta::INFO;
    public const MAP = LocationMeta::MAP;
}
