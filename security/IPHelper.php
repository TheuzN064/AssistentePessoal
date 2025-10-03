<?php
// /security/IPHelper.php

class IPHelper {
    /**
     * Obtém o endereço de IP real do visitante, mesmo por trás de um proxy como o Cloudflare.
     * @return string O endereço de IP do visitante.
     */
    public static function getRealIP() {
        // Cloudflare envia o IP real neste cabeçalho. É o mais confiável.
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        
        // Outros proxies podem usar este cabeçalho.
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }

        // Se nenhum dos cabeçalhos especiais existir, usa o padrão.
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        // Converte o endereço de loopback IPv6 para sua versão IPv4 para clareza.
        if ($ip === '::1') {
            return '127.0.0.1';
        }

        return $ip;
    }
}
