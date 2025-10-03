<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

if (!isset($_SESSION['loggedin'])) {
    http_response_code(403); exit("Acesso negado.");
}

require_once __DIR__ . '/../dao/TransactionCategoryDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$dao = new TransactionCategoryDAO();

switch ($action) {
    case 'create':
        if (!empty(trim($_POST['name']))) {
            $dao->create(trim($_POST['name']), $userId);
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $dao->delete((int)$_GET['id'], $userId);
        }
        break;
}

header("Location: categorias_gastos.php");
exit();