<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); exit("Acesso negado.");
}
require_once __DIR__ . '/../dao/GroupDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$groupDao = new GroupDAO();

switch ($action) {
    case 'create':
        if (isset($_POST['name']) && !empty(trim($_POST['name']))) {
            // Usa o método que já tínhamos para criar ou encontrar, evitando duplicatas.
            $groupDao->createOrFind($_POST['name'], $userId);
        }
        break;

    case 'update':
        if (isset($_POST['id'], $_POST['name'])) {
            $groupDao->update((int)$_POST['id'], $_POST['name'], $userId);
        }
        break;
        
    case 'delete':
        if (isset($_GET['id'])) {
            $groupDao->delete((int)$_GET['id'], $userId);
        }
        break;
}

header('Location: grupos.php');
exit();
