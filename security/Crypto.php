<?php
class Crypto {
    private const CYPHER_METHOD = 'aes-256-gcm';

    public static function encrypt($plaintext, $masterPassword) {
        if (empty($plaintext) || empty($masterPassword)) return '';
        $key = self::getKey($masterPassword);
        $iv_len = openssl_cipher_iv_length(self::CYPHER_METHOD);
        $iv = openssl_random_pseudo_bytes($iv_len);
        $ciphertext = openssl_encrypt($plaintext, self::CYPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) return false;
        return base64_encode($iv . $tag . $ciphertext);
    }

    public static function decrypt($encrypted_b64, $masterPassword) {
        if (empty($encrypted_b64) || empty($masterPassword)) return '';
        $key = self::getKey($masterPassword);
        $decoded = base64_decode($encrypted_b64);
        $iv_len = openssl_cipher_iv_length(self::CYPHER_METHOD);
        $iv = substr($decoded, 0, $iv_len);
        $tag_len = 16;
        $tag = substr($decoded, $iv_len, $tag_len);
        $ciphertext = substr($decoded, $iv_len + $tag_len);
        return openssl_decrypt($ciphertext, self::CYPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }

    private static function getKey($masterPassword) {
        return hash('sha256', $masterPassword, true);
    }
}
