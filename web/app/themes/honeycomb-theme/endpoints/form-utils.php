<?php

declare(strict_types=1);

namespace SMHG;

use ReCaptcha\ReCaptcha;

/**
 * Creates an arg configuration for use in the `args` field for a
 * WP REST API endpoint.
 *
 * This configuration is for a required textfield.
 *
 * @param fieldName - name of the submitted form field.
 * @param message - error message to display if the field is not valid.
 *
 * @return (mixed)[] - configuration for an arg in the `args` array of a
 * WP REST API endpoint.
 */
function formText(
    string $fieldName,
    string $message,
    bool $required = true
): array {

    return [
        $fieldName => [
           'required' => $required,
           'validate_callback' =>
               static function ($param) use ($message, $required) {
                if ($required && (!is_string($param) || strlen($param) === 0)) {
                    return new \WP_Error(
                        'not_empty',
                        $message,
                    );
                }

                return true;
               },
        ],
    ];
}

/**
 * Creates an arg configuration for use in the `args` field for a
 * WP REST API endpoint.
 *
 * This configuration is for a checkbox.
 *
 * @param fieldName - name of the submitted form field.
 * @param message - error message to display if the field is not valid.
 *
 * @return (mixed)[] - configuration for an arg in the `args` array of a
 * WP REST API endpoint.
 */
function formBool(
    string $fieldName,
    string $message,
    bool $required = true
): array {

    return [
        $fieldName => [
            'required' => $required,
            'validate_callback' =>
                static function ($param) use ($message, $required) {
                    if (!is_bool($param)) {
                        return new \WP_Error('not_bool', $message);
                    }

                    if ($required && !wp_validate_boolean($param)) {
                        return new \WP_Error('not_empty', $message);
                    }

                    return true;
                },
        ],
    ];
}

/**
 * Creates an arg configuration for use in the `args` field for a
 * WP REST API endpoint.
 *
 * This configuration is for a textfield with a URL value.
 *
 * @param fieldName - name of the submitted form field.
 * @param message - error message to display if the field is not valid.
 * @param required - if this field is required.  Defaults to false.
 *
 * @return (mixed)[] - configuration for an arg in the `args` array of a
 * WP REST API endpoint.
 */
function formUrl(
    string $fieldName,
    string $message,
    bool $required = true
): array {

    return [
        $fieldName => [
            'required' => $required,
            'validate_callback' => static function ($param) use ($message, $required) {
                if (!is_string($param) || ($required && strlen($param) === 0)) {
                    return new \WP_Error('not_empty', $message);
                }
                if (strlen($param) > 0 && !wp_http_validate_url($param)) {
                    return new \WP_Error('not_url', $message);
                }
                return true;
            },
        ],
    ];
}

/**
 * Creates an arg configuration for use in the `args` field for a
 * WP REST API endpoint.
 *
 * This configuration is for a required textfield with an email value.
 *
 * @param fieldName - name of the submitted form field.
 * @param message - error message to display if the field is not valid.
 * @param required - if this field is required.  Defaults to false.
 *
 * @return (mixed)[] - configuration for an arg in the `args` array of a
 * WP REST API endpoint.
 */
function formEmail(
    string $fieldName,
    string $message,
    bool $required = true
): array {

    return [
        $fieldName => [
            'required' => $required,
            'validate_callback' => static function ($param) use ($message, $required) {
                if (!is_string($param) || ($required && strlen($param) === 0)) {
                    return new \WP_Error('not_empty', $message);
                }
                if (strlen($param) > 0 && !is_email($param)) {
                    return new \WP_Error('not_email', $message);
                }

                return true;
            },
        ],
    ];
}

/**
 * Creates an arg configuration for use in the `args` field for a
 * WP REST API endpoint.
 *
 * This configuration is for a required reCAPTCHA value.
 *
 * @param recaptchaAction - the action that the user is attempting to
 * perform with the form post.
 *
 * @return (mixed)[] - configuration for an arg in the `args` array of a
 * WP REST API endpoint.
 */
function formRecaptcha(string $recaptchaAction): array
{
    return [
        'recaptcha' => [
            'required' => true,
            'validate_callback' =>
                static fn($param) => (new ReCaptcha(
                    RECAPTCHA_SECRET_KEY
                ))
                    ->setExpectedHostname(parse_url(APP_FRONTEND_URL)['host'])
                    ->setExpectedAction($recaptchaAction)
                    ->setScoreThreshold(0.9)
                    ->verify($param, getClientIp())
                    ->isSuccess(),
        ],
    ];
}

/**
 * Formats the given field's value and label for display in a multiline
 * email.
 *
 * @param fieldName - name of the submitted form field.
 * @param label - name of the form field in the email text.
 * @param params - key-value array of submitted form field values.
 *
 * @return string - formatted label and text value for the given form
 * field.
 */
function formatLabelAndField(
    string $fieldName,
    string $label,
    array $params
): string {

    $value = $params[$fieldName];
    $formatted = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
    return "{$label}:\n{$formatted}\n\n";
}
