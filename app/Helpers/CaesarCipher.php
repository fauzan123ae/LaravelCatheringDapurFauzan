<?php

namespace App\Helpers;

class CaesarCipher
{
    public static function encrypt(string $text, int $shift = 5): string
    {
        $result = '';
        $shift = $shift % 26;

        foreach (str_split($text) as $char) {
            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $base = ctype_upper($char) ? 65 : 97;
                $result .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
                $result .= $char;
            }
        }

        return $result;
    }
}
