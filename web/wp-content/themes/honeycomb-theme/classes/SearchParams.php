<?php

declare(strict_types=1);

namespace SMHG;

/**
 * class SearchParams houses search endpoints' common parameter keys.
 */
abstract class SearchParams
{
    public const CATEGORIES = 'categories';
    public const TYPES = 'types';
    public const AUDIENCES = 'audiences'; // used to be ages
    public const START_DATE = 'startDate';
    public const END_DATE = 'endDate';
    public const TIME_PERIOD = 'timePeriod';
    public const HOW_TO_ACCESS = 'howToAccess';
    public const DISTANCE_RANGE = 'distance';
    public const DISTANCE_LAT = 'lat';
    public const DISTANCE_LONG = 'lng';
    public const SERVICE_ID = 'serviceId';
}
