<?php
// /security/Logger.php
require_once __DIR__ . '/IPHelper.php'; // Inclui o novo ajudante

class Logger {
    private static $logFile = __DIR__ . '/../app_log.txt';

    public static function log($level, $message) {
        date_default_timezone_set('America/Sao_Paulo');
        $timestamp = date('Y-m-d H:i:s');
        
        // CORREÇÃO: Usa o IPHelper para obter o IP real do visitante
        $ip = IPHelper::getRealIP();
        
        $logEntry = sprintf("[%s] [%s] [%s] %s\n", $timestamp, $ip, strtoupper($level), $message);
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }
}

