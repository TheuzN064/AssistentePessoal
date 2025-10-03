<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

require_once __DIR__ . '/../vendor/autoload.php'; // Adicionado para usar a biblioteca
require_once __DIR__ . '/../models/Authenticator.php';
require_once __DIR__ . '/../dao/AuthenticatorDAO.php';

use OTPHP\TOTP; // Adicionado

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'];
$authDao = new AuthenticatorDAO();

switch ($action) {
    case 'create':
        if (isset($_POST['password_id'], $_POST['secret_key']) && !empty($_POST['password_id'])) {
            
            // Normaliza a chave: remove espaços e converte para maiúsculas
            $secretKey = strtoupper(str_replace(' ', '', trim($_POST['secret_key'])));

            // VALIDAÇÃO: Tenta criar um objeto TOTP com a chave. Se falhar, a chave é inválida.
            try {
                TOTP::create($secretKey);
            } catch (Exception $e) {
                // Se a chave for inválida, redireciona com uma mensagem de erro
                $_SESSION['2fa_error'] = "A chave secreta fornecida é inválida.";
                header('Location: autenticadores.php');
                exit();
            }

            $auth = new Authenticator();
            $auth->setUserId($userId);
            $auth->setPasswordId((int)$_POST['password_id']);
            $auth->setSecretKey($secretKey);
            
            $authDao->create($auth, $masterPassword);
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $authDao->delete((int)$_GET['id'], $userId);
        }
        break;
}

header('Location: autenticadores.php');
exit();
