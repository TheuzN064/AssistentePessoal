<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

$action = $_GET['action'] ?? null;

if ($action === 'clear') {
    $logFile = __DIR__ . '/../app_log.txt';
    if (file_exists($logFile)) {
        // Apaga o conteúdo do arquivo
        file_put_contents($logFile, '');
    }
}

// Redireciona de volta para a página de logs
header('Location: logs.php');
exit();
