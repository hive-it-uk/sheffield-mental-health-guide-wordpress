<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Format an Advanced Custom Field date
 * field to a standard ISO8601 date.
 */
function formatACFDateToIso(string $date): string
{
    if (!$date) {
        return '';
    }

    $parsed = \DateTime::createFromFormat('Ymd', $date);

    return $parsed->format('Y-m-d');
}
