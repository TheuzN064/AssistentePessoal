<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['master_password_session'])) {
    http_response_code(403); exit("Acesso negado ou cofre bloqueado.");
}
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../dao/SubscriptionDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'];
$subDao = new SubscriptionDAO();

switch ($action) {
    case 'create':
        if (isset($_POST['name'], $_POST['value']) && !empty($_POST['renewal_day'])) {
            $sub = new Subscription();
            $sub->setUserId($userId);
            $sub->setName($_POST['name']);
            $sub->setValue($_POST['value']);
            $sub->setRenewalDay((int)$_POST['renewal_day']);
            $sub->setIsShared(isset($_POST['is_shared']) ? 1 : 0);
            $sub->setPasswordId(empty($_POST['password_id']) ? null : (int)$_POST['password_id']);
            $sub->setCardId(empty($_POST['card_id']) ? null : (int)$_POST['card_id']);

            $newSubscriptionId = $subDao->create($sub, $masterPassword);

            if ($newSubscriptionId && $sub->getIsShared() && !empty($_POST['people_ids'])) {
                $peopleIds = $_POST['people_ids'];
                $subDao->linkPeopleToSubscription($newSubscriptionId, $peopleIds);
            }
        }
        break;

    // NOVA AÇÃO PARA SALVAR OS PAGAMENTOS
    case 'update_payments':
        if (isset($_POST['subscription_id'])) {
            $subscriptionId = (int)$_POST['subscription_id'];
            $paidPersonIds = $_POST['paid_people_ids'] ?? []; // Pega o array de pessoas marcadas
            $month = date('n');
            $year = date('Y');
            $subDao->updateMonthlyPayments($subscriptionId, $paidPersonIds, $month, $year);
        }
        break;
}

header('Location: assinaturas.php');
exit();
