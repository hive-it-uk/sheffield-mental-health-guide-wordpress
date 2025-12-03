<?php

declare(strict_types=1);

namespace SMHG;

use DrewM\MailChimp\MailChimp;

add_action(
    'rest_api_init',
    static function (): void {
        register_rest_route(
            'smhg/v1',
            'newsletter',
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'args' => [
                    'email' => [
                        'required' => true,
                        'validate_callback' => static function ($param) {
                            if (!is_string($param) || !is_email($param)) {
                                return new \WP_Error('not_email', 'Email is not valid');
                            }

                            return true;
                        },
                    ],
                ],
                'permission_callback' => '__return_true',
                'callback' => static function (\WP_REST_Request $request) {
                    $params = $request->get_json_params();

                    if (!defined('MAILCHIMP_API_KEY') || !defined('MAILCHIMP_LIST_ID')) {
                        return null;
                    }

                    $mailchimp = new MailChimp(MAILCHIMP_API_KEY);

                    $listId = MAILCHIMP_LIST_ID;
                    $options = [
                        'email_address' => $params['email'],
                        'status' => 'subscribed',
                        'email_type' => 'html',
                        'merge_fields' => [
                            'EMAIL' => $params['email'],
                        ],
                    ];

                    $response = $mailchimp->post("lists/{$listId}/members", $options);

                    if (!$mailchimp->success()) {
                        error_log("Could not add '{$params['email']}' to mailing list. Details: {$response['detail']}");

                        return new \WP_Error(
                            'server_error',
                            'Could not add to newsletter',
                            ['status' => 500]
                        );
                    }

                    return null;
                },
            ]
        );
    }
);
