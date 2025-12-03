<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Generate an access token given a user.
 *
 * @param \WP_User $user
 * @param int $duration The max duration (in seconds) that
 *                      the token is valid for.
 * @return string|bool An encrypted string or false.
 */
function generateAccessToken(\WP_User $user, int $duration)
{
    if (!$user->ID) {
        return false;
    }

    $time = time();

    // We use a super basic access token
    // implementation instead of JWTs so we
    // don't need to do too much validation.
    return encrypt(
        serialize(
            [
                'user_id' => $user->ID,
                'expires' => $time + $duration,
                'issued' => $time,
            ]
        )
    );
}

/**
 * Get a WP_User given an access token.
 *
 * @param string $token A base 64 encoded string.
 * @return \WP_User|bool A WP_User object or false.
 */
function getUserFromAccessToken(string $token)
{
    $decrypted = decrypt($token);

    if (!$decrypted) {
        return false;
    }

    $data = unserialize($decrypted);

    if (!is_array($data)) {
        return false;
    }

    if (time() > $data['expires']) {
        return false;
    }

    return get_user_by('ID', absint($data['user_id']));
}

/**
 * Encrypt a value.
 *
 * @param string $value The value to encrypt.
 *
 * @return string|bool The encrypted value as a base 64 encoded string or false.
 *
 */
function encrypt(string $value)
{
    $key = AUTH_ENCRYPTION_KEY;

    if (!$key) {
        return false;
    }

    try {
        $nonce = random_bytes(AUTH_TOKEN_NONCE_LENGTH);
        $cipherText = sodium_crypto_secretbox($value, $nonce, $key);

        if (!$cipherText) {
            return false;
        }

        $mac = sodium_crypto_auth($cipherText, $key);

        return sodium_bin2hex(
            $nonce . $mac . $cipherText
        );
    } catch (\Throwable $exception) {
        return false;
    }
}

/**
 * Decrypt a value.
 *
 * @param string $value The base 64 encoded value.
 * @return string|bool The decrypted value or false.
 */
function decrypt(string $value)
{
    $key = AUTH_ENCRYPTION_KEY;

    if (!$key) {
        return false;
    }

    try {
        $value = sodium_hex2bin($value);

        $nonceLength = AUTH_TOKEN_NONCE_LENGTH;
        $hashLength = SODIUM_CRYPTO_AUTH_BYTES;

        $nonce = substr($value, 0, $nonceLength);
        $mac = substr($value, $nonceLength, $hashLength);
        $cipherText = substr($value, $nonceLength + $hashLength);

        if (sodium_crypto_auth_verify($mac, $cipherText, $key)) {
            return sodium_crypto_secretbox_open(
                $cipherText,
                $nonce,
                $key
            );
        }
    } catch (\Throwable $exception) {
        return false;
    }

    return false;
}
