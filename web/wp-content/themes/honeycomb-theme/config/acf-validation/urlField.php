<?php

declare(strict_types=1);

namespace SMHG;

/**
 * @return string|bool
 */
function validateUrl($valid, $value)
{
    // Bail early if value is already invalid.
    if ($valid !== true) {
        return $valid;
    }

    if ($value === '' || $value === null) {
        return $valid;
    }

    $isURL = preg_match('/^(https?:\/\/)?([a-z0-9\-]+\.)+[a-z]{2,}(:\d{1,5})?(\/\S*)?$/', $value);

    if (!$isURL) {
        return __('Value must be a valid URL');
    }

    return true;
}

// Apply to all fields.
add_filter('acf/validate_value/type=url', __NAMESPACE__ . '\validateUrl', 10, 4);
