<?php
class ConexaoPDO {
    private static $instance;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance() {
        // Se o modo pânico estiver ativo (arquivo panic.flag existe), nem tenta conectar.
        if (file_exists(__DIR__ . '/../panic.flag')) {
            // Lança uma exceção específica para o modo pânico.
            throw new Exception("PANIC_MODE_ACTIVE");
        }

        if (!isset(self::$instance)) {
            $host = 'localhost';
            $db   = 'assis_pessoal';
            $user = 'lucena';
            $pass = '142536475869Ss@';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // Se a conexão falhar (ex: usuário bloqueado), lança a exceção para ser tratada.
                throw $e;
            }
        }
        return self::$instance;
    }
}
