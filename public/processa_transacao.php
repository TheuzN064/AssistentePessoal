<?php
// /public/processa_transacao.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../security/SessionManager.php';
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Logger.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../dao/TransactionDAO.php';

SessionManager::checkActivity();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['master_password_session'])) {
    http_response_code(403); 
    exit("Acesso negado ou cofre bloqueado.");
}

$action = $_REQUEST['action'] ?? null;
$month = $_REQUEST['month'] ?? date('n');
$year = $_REQUEST['year'] ?? date('Y');

try {
    $userId = $_SESSION['user_id'];
    $masterPassword = $_SESSION['master_password_session'];
    $dao = new TransactionDAO();

    switch ($action) {
        case 'create':
            if (empty(trim($_POST['name'])) || empty(trim($_POST['value'])) || empty(trim($_POST['transaction_date']))) {
                throw new Exception("Os campos Nome, Valor e Data são obrigatórios.");
            }

            $valueCleaned = str_replace(',', '.', preg_replace('/[^\d,]/', '', $_POST['value']));

            $transaction = new Transaction();
            $transaction->setUserId($userId);
            $transaction->setName($_POST['name']);
            $transaction->setDescription($_POST['description']);
            $transaction->setValue($valueCleaned);
            $transaction->setType($_POST['type']);
            $transaction->setTransactionDate($_POST['transaction_date']);
            $transaction->setCategoryId(empty($_POST['category_id']) ? null : (int)$_POST['category_id']);
            
            $result = $dao->create($transaction, $masterPassword);

            if ($result === true) {
                $_SESSION['form_success'] = "Lançamento adicionado com sucesso!";
            } else {
                throw new Exception($result);
            }
            break;

        case 'delete':
            if (isset($_GET['id'])) {
                if ($dao->delete((int)$_GET['id'], $userId)) {
                    $_SESSION['form_success'] = "Lançamento apagado com sucesso!";
                } else {
                    throw new Exception("Ocorreu um erro ao apagar o lançamento.");
                }
            }
            break;
    }
} catch (Throwable $e) {
    Logger::log('ERROR', 'Erro fatal em processa_transacao.php: ' . $e->getMessage());
    $_SESSION['form_error'] = "Erro: " . $e->getMessage();
}

header("Location: gastos.php?month={$month}&year={$year}");
exit();