<?php

declare(strict_types=1);

/** @var \WP */
global $wp;

if (is_archive() || is_singular()) {
    header('Location:' . APP_FRONTEND_URL . '/' . $wp->request, true, 303);
}
