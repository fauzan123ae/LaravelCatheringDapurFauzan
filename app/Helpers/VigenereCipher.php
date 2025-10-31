<?php

namespace App\Helpers;

class VigenereCipher
{
    public static function encrypt(string $plaintext, string $key): string
    {
        $plaintext   = strtoupper($plaintext);
        $key         = strtoupper($key);
        $ciphertext  = "";
        $keyLength   = strlen($key);

        for ($i = 0; $i < strlen($plaintext); $i++) {
            $char = $plaintext[$i];

            if (ctype_alpha($char)) {
                $p = ord($char) - ord('A');
                $k = ord($key[$i % $keyLength]) - ord('A');
                $c = ($p + $k) % 26;
                $ciphertext .= chr($c + ord('A'));
            } else {
                $ciphertext .= $char;
            }
        }

        return $ciphertext;
    }

    public static function decrypt(string $ciphertext, string $key): string
    {
        $ciphertext = strtoupper($ciphertext);
        $key        = strtoupper($key);
        $plaintext  = "";
        $keyLength  = strlen($key);

        for ($i = 0; $i < strlen($ciphertext); $i++) {
            $char = $ciphertext[$i];

            if (ctype_alpha($char)) {
                $c = ord($char) - ord('A');
                $k = ord($key[$i % $keyLength]) - ord('A');
                $p = ($c - $k + 26) % 26;
                $plaintext .= chr($p + ord('A'));
            } else {
                $plaintext .= $char;
            }
        }

        return $plaintext;
    }
}
