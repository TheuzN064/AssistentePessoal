<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

require_once __DIR__ . '/../models/Bill.php';
require_once __DIR__ . '/../dao/BillDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'];

$billDao = new BillDAO();

// Pega 'month' e 'year' de $_REQUEST para funcionar com GET (delete) e POST (create, update)
$month = $_REQUEST['month'] ?? date('n');
$year = $_REQUEST['year'] ?? date('Y');

switch ($action) {
    case 'create':
        if (isset($_POST['name'], $_POST['value'], $_POST['due_date'])) {
            
            // --- CORREÇÃO: Limpa o valor monetário antes de salvar ---
            $valueRaw = $_POST['value'];
            // Remove tudo que não for dígito ou vírgula
            $valueCleaned = preg_replace('/[^\d,]/', '', $valueRaw);
            // Troca a vírgula por ponto para o formato numérico do PHP
            $valueNumeric = str_replace(',', '.', $valueCleaned);

            if (!empty($valueNumeric) && (float)$valueNumeric > 0) {
                $bill = new Bill();
                $bill->setUserId($userId);
                $bill->setName($_POST['name']);
                $bill->setValue($valueNumeric); // Salva o valor numérico limpo
                $bill->setDueDate($_POST['due_date']);
                $billDao->create($bill, $masterPassword);
            }
        }
        break;

    case 'update_paid_statuses':
        if (isset($_POST['month'], $_POST['year'])) {
            $paidBillIds = $_POST['paid_bills_ids'] ?? []; // Pega o array de contas marcadas
            $billDao->updatePaidStatusesForMonth($userId, (int)$_POST['month'], (int)$_POST['year'], $paidBillIds);
        }
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $billDao->delete((int)$_GET['id'], $userId);
        }
        break;
}

header("Location: contas.php?month={$month}&year={$year}");
exit();
