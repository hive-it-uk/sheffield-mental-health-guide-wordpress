<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'rest_api_init',
    static function (): void {
        register_rest_route(
            'smhg/v1',
            'contact',
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'args' => array_merge(
                    formText('name', 'Name must be not be empty'),
                    formEmail('email', 'Email is not valid'),
                    formText('message', 'Message must not be empty'),
                    formRecaptcha('contact_us'),
                ),
                'permission_callback' => '__return_true',
                'callback' => static function (\WP_REST_Request $request): void {
                    $params = $request->get_json_params();

                    // This will be a plaintext email. We could improve this
                    // by rendering out a HTML email.
                    // TODO SMHG-99: Send HTML emails instead of plaintext
                    wp_mail(
                        'mhguide@sheffieldflourish.co.uk',
                        'Contact us enquiry',
                        "A person has used Sheffield Mental Health Guide's 'Contact us' form with the following details:\n\n" .
                        formatLabelAndField('name', 'Name', $params) .
                        formatLabelAndField('email', 'Email', $params) .
                        formatLabelAndField('message', 'Message', $params)
                    );
                },
            ]
        );
    }
);
