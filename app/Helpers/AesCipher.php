<?php

namespace App\Helpers;

class AesCipher
{
    private static string $cipher = 'AES-256-CBC';


    private static string $key = 'MY_SECRET_KEY_32_CHARACTERS_LONG!!';

    public static function encrypt(string $plaintext): string
    {
        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt(
            $plaintext,
            self::$cipher,
            self::$key,
            0,
            $iv
        );


        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(string $ciphertext): string|false
    {
        $ciphertext = base64_decode($ciphertext);

        $ivLength = openssl_cipher_iv_length(self::$cipher);
        $iv = substr($ciphertext, 0, $ivLength);
        $encrypted = substr($ciphertext, $ivLength);

        return openssl_decrypt(
            $encrypted,
            self::$cipher,
            self::$key,
            0,
            $iv
        );
    }
}
