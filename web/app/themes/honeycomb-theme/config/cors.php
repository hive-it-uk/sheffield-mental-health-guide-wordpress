<?php

declare(strict_types=1);

add_action(
    'rest_api_init',
    static function (): void {
        header('Access-Control-Allow-Origin: ' . APP_FRONTEND_URL);
    }
);
