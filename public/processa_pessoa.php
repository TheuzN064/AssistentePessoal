<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['master_password_session'])) {
    http_response_code(403); exit("Acesso negado ou cofre bloqueado.");
}
require_once __DIR__ . '/../dao/PersonDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'];
$personDao = new PersonDAO();

switch ($action) {
    case 'create':
        if (isset($_POST['name'])) {
            $personDao->create($_POST['name'], $userId, $masterPassword);
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $personDao->delete((int)$_GET['id'], $userId);
        }
        break;
}

header('Location: pessoas.php');
exit();
