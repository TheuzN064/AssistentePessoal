<?php
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../security/SecurityManager.php';
require_once __DIR__ . '/../security/Logger.php';
require_once __DIR__ . '/../security/SessionManager.php'; // Adicionado

const MAX_ATTEMPTS_BEFORE_PANIC = 3;

if (SecurityManager::isPanicModeActive()) {
    header('Location: index.php?error=panic');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['master-password'])) {
    $username = $_POST['username'];
    
    $user = new User(); // Supondo que a classe User está em models/User.php
    $user->setUsername($username);
    $user->setPassword($_POST['master-password']);

    $userDao = new UserDAO();
    $loginData = $userDao->validateLogin($user);

    if ($loginData !== false) {
        Logger::log('INFO', "Login bem-sucedido para o usuário '{$username}'.");
        
        // Usa o SessionManager para iniciar a sessão de forma centralizada
        SessionManager::login($loginData['id'], $loginData['username']);
        
        header('Location: dashboard.php');
        exit();
    } else {
        session_start(); // Inicia a sessão para contar as tentativas
        $attempts = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['login_attempts'] = $attempts;
        
        Logger::log('WARNING', "Tentativa de login falhou para '{$username}'. Tentativa {$attempts}/" . MAX_ATTEMPTS_BEFORE_PANIC . ".");

        if ($attempts >= MAX_ATTEMPTS_BEFORE_PANIC) {
            Logger::log('PANIC', "Limite de tentativas atingido. Ativando modo pânico.");
            SecurityManager::triggerPanicMode(['username' => $username, 'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A']);
            unset($_SESSION['login_attempts']);
            header('Location: index.php?error=panic');
            exit();
        }
        header('Location: index.php?error=1');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
