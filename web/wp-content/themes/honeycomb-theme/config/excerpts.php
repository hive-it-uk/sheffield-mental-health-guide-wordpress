<?php

declare(strict_types=1);

add_filter(
    'get_the_excerpt',
    /**
     * Truncate excerpt to a single sentence.
     */
    static function (string $excerpt) {
        // Clean tokens that can interrupt the regex
        $cleaned = str_replace('&nbsp;', ' ', $excerpt);

        // Split excerpt into sentences. This uses a general purpose pattern that
        // does NOT account for all cases, but should be 'good enough'.
        $parts = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $cleaned, 2);

        return $parts[0];
    }
);
