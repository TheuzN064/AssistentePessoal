<?php
// /security/SessionManager.php

class SessionManager {
    // Tempo em segundos para a sessão de login expirar (30 minutos)
    private const LOGIN_TIMEOUT = 1800; 
    // Tempo em segundos para o cofre ser bloqueado (5 minutos)
    private const VAULT_TIMEOUT = 300;

    /**
     * Inicia e valida a sessão a cada carregamento de página.
     * Deve ser chamado no topo de todas as páginas seguras.
     */
    public static function checkActivity() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Verifica se o usuário está logado
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            self::logout();
        }

        // 2. Verifica o tempo de inatividade
        if (isset($_SESSION['last_activity'])) {
            $inactive_time = time() - $_SESSION['last_activity'];

            // Se a inatividade exceder o tempo de login, desloga completamente
            if ($inactive_time > self::LOGIN_TIMEOUT) {
                self::logout();
            }

            // Se a inatividade exceder o tempo do cofre, apenas tranca o cofre
            if ($inactive_time > self::VAULT_TIMEOUT) {
                unset($_SESSION['master_password_session']);
            }
        }

        // 3. Atualiza o tempo da última atividade para a hora atual
        $_SESSION['last_activity'] = time();
    }

    /**
     * Inicia uma nova sessão de login.
     */
    public static function login($userId, $username) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['last_activity'] = time();
    }

    /**
     * Encerra a sessão do usuário.
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?reason=session_expired');
        exit();
    }
}

