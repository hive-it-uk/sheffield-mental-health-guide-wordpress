<?php

declare(strict_types=1);

namespace SMHG;

function createActivityInstances(int $postId): ?object
{
    removeCurrentActivityInstances($postId);

    $startDate = get_post_meta($postId, ActivityMeta::START_DATE, true);
    if (!isValidDate($startDate)) {
        error_log(print_r("$postId, start_date is invalid!"));
        return null;
    }
    $startDate = new \DateTime($startDate);

    $frequency = get_post_meta($postId, ActivityMeta::FREQUENCY, true);
    if ($frequency === 'oneoff') {
        return createSingleActivityInstance($postId, $startDate);
    }

    $endDate = get_post_meta($postId, ActivityMeta::END_DATE, true);
    if (!isValidDate($endDate)) {
        error_log(print_r("$postId, end_date is invalid!"));
        return null;
    }
    $endDate = new \DateTime($endDate);
    if ($endDate < new \DateTime('yesterday')) {
        return null;
    }

    $weekday = get_post_meta($postId, ActivityMeta::WEEKDAY, true);

    if ($frequency === 'weekly') {
        return createActivityInstancesWeekly($postId, $startDate, $endDate, $weekday);
    }

    if ($frequency === 'monthly') {
        return createActivityInstancesMonthly($postId, $startDate, $endDate, $weekday);
    }
}

function removeCurrentActivityInstances(int $postId): void
{
    delete_post_meta($postId, ActivityMeta::ACTIVITY_INSTANCES);
}

function createSingleActivityInstance(int $postId, \DateTime $activityInstanceDate): void
{
    if ($activityInstanceDate < new \DateTime('today midnight')) {
        return;
    }

    add_post_meta($postId, ActivityMeta::ACTIVITY_INSTANCES, $activityInstanceDate->format('Y-m-d'));
}

function createActivityInstancesWeekly(int $postId, \DateTime $startDate, \DateTime $endDate, $weekday): void
{
    global $wpdb;

    $weeklyInterval = get_post_meta($postId, ActivityMeta::WEEKLY_INTERVAL, true);
    if (!$weeklyInterval) {
        $weeklyInterval = 1;
    }

    $firstDate = new \DateTime($startDate->format('Y-m-d'));

    if ($weekday !== $firstDate->format('l')) {
        $firstDate->modify("next $weekday");
    }

    $activityInstanceDate = $firstDate;
    while ($activityInstanceDate >= $startDate && $activityInstanceDate <= $endDate) {
        createSingleActivityInstance($postId, $activityInstanceDate);

        $daysToAdd = $weeklyInterval * 7;
        $activityInstanceDate->modify("+$daysToAdd days");
    }
}

function createActivityInstancesMonthly(int $postId, \DateTime $startDate, \DateTime $endDate, $weekday): void
{
    global $wpdb;

    $monthlyWeek = get_post_meta($postId, ActivityMeta::MONTHLY_WEEK, true);

    if ($monthlyWeek === '1' || $monthlyWeek === '2' || $monthlyWeek === '3' || $monthlyWeek === '4') {
        // 1,2,3,4 = 1st,2nd,3rd,4th.
        $firstOfMonthDate = new \DateTime("first day of {$startDate->format('M Y')}");

        while ($firstOfMonthDate <= $endDate) {
            $incrementingDate = new \DateTime($firstOfMonthDate->format('Y-m-d'));

            if ($weekday !== $incrementingDate->format('l')) {
                $incrementingDate->modify("next $weekday");
            }

            $weeksToAdd = $monthlyWeek - 1;
            $activityInstanceDate = $incrementingDate->modify("+$weeksToAdd weeks");

            if ($activityInstanceDate >= $startDate && $activityInstanceDate <= $endDate) {
                createSingleActivityInstance($postId, $activityInstanceDate);
            }

            $firstOfMonthDate->modify('+1 month');
        }
    } elseif ($monthlyWeek === '5') {
        // 5 = Last (Last Wednesday of the month for example).
        $firstOfMonthDate = new \DateTime("first day of {$startDate->format('M Y')}");

        while ($firstOfMonthDate <= $endDate) {
            $decrementingDate = new \DateTime("last day of {$firstOfMonthDate->format('M Y')}");

            if ($weekday !== $decrementingDate->format('l')) {
                $decrementingDate->modify("last $weekday");
            }

            $activityInstanceDate = $decrementingDate;
            if ($activityInstanceDate >= $startDate && $activityInstanceDate <= $endDate) {
                createSingleActivityInstance($postId, $activityInstanceDate);
            }
            $firstOfMonthDate->modify('+1 month');
        }
    } else {
        error_log(print_r(" invalid monthly_week: $monthlyWeek "));
        return;
    }
}

function isValidDate( $strDate, $strDateFormat = 'Ymd', $strTimezone = 'Europe/London' ): bool
{
    if (!$strDate) {
        return false;
    }
    return !! \DateTime::createFromFormat($strDateFormat, $strDate, new \DateTimeZone($strTimezone));
}

function limitActivityEndDateFromStartDate( int $postId, int $maxYearsDifference = 5, $strDateFormat = 'Ymd', $strTimezone = 'Europe/London' ): void
{

    $strStartDate = get_post_meta($postId, ActivityMeta::START_DATE, true);
    $strEndDate = get_post_meta($postId, ActivityMeta::END_DATE, true);

    if (!isValidDate($strStartDate) || !isValidDate($strEndDate)) {
        return;
    }

    $startDate = \DateTime::createFromFormat($strDateFormat, $strStartDate, new \DateTimeZone($strTimezone));
    $endDate = \DateTime::createFromFormat($strDateFormat, $strEndDate, new \DateTimeZone($strTimezone));

    $diff = $startDate->diff($endDate);
    if ($diff->y <= $maxYearsDifference) {
        return;
    }

    $reducedEndDate = $startDate->modify("+{$maxYearsDifference} years")->format($strDateFormat);
    update_field(ActivityMeta::END_DATE, $reducedEndDate, $postId);
}
