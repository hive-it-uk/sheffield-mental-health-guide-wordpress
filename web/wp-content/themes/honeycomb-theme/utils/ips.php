<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Get the client's IP address (if possible).
 *
 * We try our best to determine the IP, but like most IP
 * detection tools, we can't accurately determine where
 * the request has come from as it's super easy to
 * spoof and people use proxies.
 */
function getClientIp(): string
{
    $isForwarded = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '';

    if ($isForwarded) {
        $addr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($addr[0]);

        return rest_is_ip_address($ip) ? $ip : $_SERVER['REMOTE_ADDR'];
    }

    return $_SERVER['REMOTE_ADDR'];
}
