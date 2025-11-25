<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); exit("Acesso negado.");
}

require_once __DIR__ . '/../models/Password.php';
require_once __DIR__ . '/../dao/PasswordDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/GroupDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'] ?? null;

$passwordDao = new PasswordDAO();
$userDao = new UserDAO();
$groupDao = new GroupDAO();

switch ($action) {
    
    case 'unlock_vault':
        $redirectPage = $_POST['redirect_to'] ?? 'senhas.php';
        $redirectUrl = $redirectPage;

        if (isset($_POST['master_password_for_session'])) {
            if ($userDao->validateVaultPassword($userId, $_POST['master_password_for_session'])) {
                $_SESSION['master_password_session'] = $_POST['master_password_for_session'];
            } else {
                if ($redirectPage === 'dashboard.php') {
                    $redirectUrl .= '?error=unlock_failed';
                } else {
                    $_SESSION['vault_error'] = "Senha do cofre incorreta.";
                }
            }
        }
        header("Location: {$redirectUrl}");
        exit();

    case 'create':
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $masterPassword) {
            $groupId = null;
            if (!empty(trim($_POST['new_group_name']))) {
                $groupId = $groupDao->createOrFind($_POST['new_group_name'], $userId);
            } elseif (!empty($_POST['group_id'])) {
                $groupId = (int)$_POST['group_id'];
            }

            $pass = new Password();
            if ($action === 'update') $pass->setId((int)$_POST['id']);
            $pass->setUserId($userId);
            $pass->setGroupId($groupId);
            $pass->setName($_POST['name']);
            $pass->setDescription($_POST['description']);
            $pass->setSiteUrl($_POST['site_url']);
            $pass->setEmail($_POST['email']);
            $pass->setPasswordText($_POST['password_text']);
            
            $recoveryCodesRaw = trim($_POST['recovery_codes'] ?? '');
            $recoveryCodesArray = [];
            if (!empty($recoveryCodesRaw)) {
                foreach (explode("\n", $recoveryCodesRaw) as $line) {
                    if (!empty(trim($line))) $recoveryCodesArray[] = ['code' => trim($line), 'used' => false];
                }
            }
            $pass->setRecoveryCodes(json_encode($recoveryCodesArray));

            $newId = ($action === 'create') ? $passwordDao->create($pass, $masterPassword) : $passwordDao->update($pass, $masterPassword);
            
            if ($newId) {
                $_SESSION['pending_highlight_id'] = $newId;
            }
        }
        header('Location: senhas.php'); 
        exit();

    case 'get_details':
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !$masterPassword) {
            echo json_encode(['success' => false, 'message' => 'Requisição inválida.']); exit();
        }
        $id = (int)$_POST['id'];
        $encryptedData = $passwordDao->getPasswordById($id, $userId);
        if (!$encryptedData) { echo json_encode(['success' => false, 'message' => 'Não encontrado.']); exit(); }
        
        $decryptedData = [
            'success' => true, 'id' => $encryptedData['id'], 'name' => Crypto::decrypt($encryptedData['name'], $masterPassword),
            'group_id' => $encryptedData['group_id'],
            'group_name' => $encryptedData['group_name'],
            'description' => Crypto::decrypt($encryptedData['description'], $masterPassword),
            'site_url' => Crypto::decrypt($encryptedData['site_url'], $masterPassword),
            'email' => Crypto::decrypt($encryptedData['email'], $masterPassword),
            'password' => Crypto::decrypt($encryptedData['password'], $masterPassword),
            'recovery_codes' => Crypto::decrypt($encryptedData['recovery_codes'], $masterPassword),
            'secret_key' => isset($encryptedData['secret_key']) ? Crypto::decrypt($encryptedData['secret_key'], $masterPassword) : null
        ];
        echo json_encode($decryptedData); exit();

    case 'delete':
        if (isset($_GET['id'])) {
            $passwordDao->delete((int)$_GET['id'], $userId);
        }
        header('Location: senhas.php'); 
        exit();
}

header('Location: senhas.php');
exit();