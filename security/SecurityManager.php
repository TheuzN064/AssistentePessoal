<?php
require_once __DIR__ . '/Logger.php';
require_once __DIR__ . '/IPHelper.php';
require_once __DIR__ . '/EmailHelper.php'; // Inclui o novo ajudante de e-mail

class SecurityManager {
    public static $panicFile = __DIR__ . '/../panic.flag';

    public static function isPanicModeActive() {
        return file_exists(self::$panicFile);
    }

    public static function triggerPanicMode(array $contextData = []) {
        date_default_timezone_set('America/Sao_Paulo');
        $ipAddress = IPHelper::getRealIP();

        $panicInfo = [
            'triggered_at' => date('Y-m-d H:i:s'),
            'reason' => 'Múltiplas tentativas de login falharam.',
            'attempted_username' => $contextData['username'] ?? 'N/A',
            'source_ip' => $ipAddress,
            'user_agent' => $contextData['user_agent'] ?? 'N/A'
        ];

        $panicContent = json_encode($panicInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents(self::$panicFile, $panicContent);
        Logger::log('PANIC', 'MODO PÂNICO ATIVADO. Informações salvas em panic.flag.');

        // ENVIA O E-MAIL DE ALERTA
        EmailHelper::sendPanicAlert($panicInfo);

        // Bloqueia a conta do usuário no banco de dados
        $app_db_user = 'lucena';
        $admin_host = '127.0.0.1';
        $admin_user = 'seguranca_admin'; 
        $admin_pass = 'Pn6002@php#';

        try {
            $dsn = "mysql:host={$admin_host}";
            $pdo_admin = new PDO($dsn, $admin_user, $admin_pass);
            $pdo_admin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "ALTER USER '{$app_db_user}'@'localhost' ACCOUNT LOCK";
            $pdo_admin->exec($sql);
            $pdo_admin->exec("FLUSH PRIVILEGES");
            
            Logger::log('PANIC', "A conta do usuário '{$app_db_user}' foi BLOQUEADA no banco de dados.");
            return true;
        } catch (PDOException $e) {
            Logger::log('ERROR', "FALHA AO BLOQUEAR CONTA DURANTE O PÂNICO: " . $e->getMessage());
            return false;
        }
    }
}
