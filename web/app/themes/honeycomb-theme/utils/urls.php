<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Replace any occurrences of the home URL with the frontend URL.
 */
function replaceFrontendUrl(string $url): string
{
    return str_replace(WP_HOME, APP_FRONTEND_URL, $url);
}

/**
 * Convenience wrapper around {@see \SMHG\replaceFrontendUrl()}
 * to modify URLs more easily by reference.
 *
 * This is a bit hacky but otherwise most of these
 * replacements are super verbose and error-prone.
 */
function replaceFrontendUrlByReference(?string &$url): void
{
    if ($url === null) {
        return;
    }

    $url = replaceFrontendUrl($url);
}
