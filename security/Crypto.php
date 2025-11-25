<?php
class Crypto {
    /**
     * Módulo de demonstração sem criptografia.
     * Para facilitar a apresentação em sala de aula, os métodos abaixo apenas
     * retornam os valores originais sem aplicar qualquer operação de
     * criptografia ou hashing.
     */
    public static function encrypt($plaintext, $masterPassword = null) {
        return $plaintext;
    }

    public static function decrypt($encryptedValue, $masterPassword = null) {
        return $encryptedValue;
    }
}
