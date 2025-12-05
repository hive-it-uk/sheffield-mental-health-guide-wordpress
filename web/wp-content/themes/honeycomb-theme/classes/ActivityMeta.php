<?php

declare(strict_types=1);

namespace SMHG;

/**
 * class ActivityMeta houses activity meta keys.
 */
abstract class ActivityMeta
{
    public const ORGANISER_NAME = 'activity_organiser_name';
    public const ORGANISER_EMAIL = 'activity_organiser_email';
    public const ORGANISER_WEBSITE = 'activity_organiser_website';
    public const ORGANISER_PHONE = 'activity_organiser_phone';
    public const ORGANISER_ORGANISING_SERVICE = 'activity_organiser_organising_service';

    public const FREQUENCY = 'activity_schedule_frequency';
    public const START_DATE = 'activity_schedule_start_date';
    public const END_DATE = 'activity_schedule_end_date';
    public const START_TIME = 'activity_schedule_start_time';
    public const END_TIME = 'activity_schedule_end_time';
    public const WEEKDAY = 'activity_schedule_frequency_weekday';
    public const WEEKLY_INTERVAL = 'activity_schedule_frequency_weekly_interval';
    public const MONTHLY_WEEK = 'activity_schedule_frequency_monthly_week';
    public const ACTIVITY_INSTANCES = 'activity_instances';
    public const HOW_TO_ACCESS = 'how_to_access';

    public const LOCATION = 'activity_location';
    public const BOOKING_REQUIRED = 'activity_booking_required';
    public const TICKET_PRICE = 'activity_ticket_price';
    public const CALL_TO_ACTION_LABEL = 'call_to_action_label';
    public const CALL_TO_ACTION_URL = 'call_to_action_url';
}
