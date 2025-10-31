<?php

namespace App\Helpers;

class CipherHelper
{
    // ==========================================================
    // 1. Caesar Cipher
    // ==========================================================
    public static function caesarEncrypt($plaintext, $shift = 3)
    {
        $ciphertext = "";
        $shift = $shift % 26;

        for ($i = 0; $i < strlen($plaintext); $i++) {
            $char = $plaintext[$i];

            if (ctype_alpha($char)) {
                $ascii = ord($char);
                $base = ctype_upper($char) ? ord('A') : ord('a');
                $ciphertext .= chr(($ascii - $base + $shift) % 26 + $base);
            } else {
                $ciphertext .= $char;
            }
        }

        return $ciphertext;
    }

    public static function caesarDecrypt($ciphertext, $shift = 3)
    {
        return self::caesarEncrypt($ciphertext, 26 - $shift);
    }

    // ==========================================================
    // 2. Vigenere Cipher
    // ==========================================================
    public static function vigenereEncrypt($plaintext, $key)
    {
        $ciphertext = "";
        $key = strtoupper($key);
        $plaintext = strtoupper($plaintext);
        $keyLen = strlen($key);

        for ($i = 0, $j = 0; $i < strlen($plaintext); $i++) {
            $char = $plaintext[$i];
            if (ctype_alpha($char)) {
                $ciphertext .= chr(((ord($char) - 65 + ord($key[$j % $keyLen]) - 65) % 26) + 65);
                $j++;
            } else {
                $ciphertext .= $char;
            }
        }

        return $ciphertext;
    }

    public static function vigenereDecrypt($ciphertext, $key)
    {
        $plaintext = "";
        $key = strtoupper($key);
        $ciphertext = strtoupper($ciphertext);
        $keyLen = strlen($key);

        for ($i = 0, $j = 0; $i < strlen($ciphertext); $i++) {
            $char = $ciphertext[$i];
            if (ctype_alpha($char)) {
                $plaintext .= chr(((ord($char) - 65 - (ord($key[$j % $keyLen]) - 65) + 26) % 26) + 65);
                $j++;
            } else {
                $plaintext .= $char;
            }
        }

        return $plaintext;
    }

    // ==========================================================
    // 3. AES Cipher
    // ==========================================================
    private static $aesKey = 'my-secret-key-123';
    private static $aesCipher = 'AES-256-CBC';

    public static function aesEncrypt($plaintext)
    {
        $ivlen = openssl_cipher_iv_length(self::$aesCipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, self::$aesCipher, self::$aesKey, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }

    public static function aesDecrypt($ciphertextBase64)
    {
        $data = base64_decode($ciphertextBase64);
        $ivlen = openssl_cipher_iv_length(self::$aesCipher);
        $iv = substr($data, 0, $ivlen);
        $ciphertext = substr($data, $ivlen);
        return openssl_decrypt($ciphertext, self::$aesCipher, self::$aesKey, 0, $iv);
    }

    // ==========================================================
    // 4. Combined Cipher (Caesar → Vigenere → AES)
    // ==========================================================
    public static function encryptCombined($plaintext, $caesarShift = 5, $vigenereKey = "ENKRIPSI")
    {
        $step1 = self::caesarEncrypt($plaintext, $caesarShift);
        $step2 = self::vigenereEncrypt($step1, $vigenereKey);
        $final = self::aesEncrypt($step2);
        return $final;
    }

    public static function decryptCombined($ciphertextBase64, $caesarShift = 5, $vigenereKey = "ENKRIPSI")
    {
        $step1 = self::aesDecrypt($ciphertextBase64);
        $step2 = self::vigenereDecrypt($step1, $vigenereKey);
        $final = self::caesarDecrypt($step2, $caesarShift);
        return $final;
    }
}
