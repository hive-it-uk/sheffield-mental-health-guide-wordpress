<?php

declare(strict_types=1);

namespace SMHG;

/**
 * @return string|bool
 */
function validatePostcode($valid, $value)
{
    // Bail early if value is already invalid.
    if ($valid !== true) {
        return $valid;
    }

    if ($value === '' || $value === null) {
        return $valid;
    }

    $regexPostcode = '/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9][A-Za-z]?))))\s?[0-9][A-Za-z]{2})$/';

    $isUKPostcode = preg_match($regexPostcode, strtoupper($value));

    if (!$isUKPostcode) {
        return __('Please enter a valid postcode.');
    }

    return $valid;
}

// Apply to all fields.
add_filter('acf/validate_value/name=' . ServiceMeta::POSTCODE, __NAMESPACE__ . '\validatePostcode', 10, 4);
add_filter('acf/validate_value/name=postcode', __NAMESPACE__ . '\validatePostcode', 10, 4);
