<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['master_password_session'])) {
    http_response_code(403); exit("Acesso negado ou cofre bloqueado.");
}
require_once __DIR__ . '/../models/Card.php';
require_once __DIR__ . '/../dao/CardDAO.php';

$action = $_REQUEST['action'] ?? null;
$userId = $_SESSION['user_id'];
$masterPassword = $_SESSION['master_password_session'];
$cardDao = new CardDAO();

switch ($action) {
    case 'create':
        if (isset($_POST['card_name'])) {
            $card = new Card();
            $card->setUserId($userId);
            $card->setCardName($_POST['card_name']);
            $card->setCardHolderName($_POST['card_holder_name']);
            $card->setCardNumber($_POST['card_number']);
            $card->setExpiryDate($_POST['expiry_date']);
            $card->setCvv($_POST['cvv']);
            $cardDao->create($card, $masterPassword);
        }
        header('Location: cartoes.php');
        exit();

    case 'update':
        if (isset($_POST['id'], $_POST['card_name'])) {
            $card = new Card();
            $card->setId((int)$_POST['id']);
            $card->setUserId($userId);
            $card->setCardName($_POST['card_name']);
            $card->setCardHolderName($_POST['card_holder_name']);
            $card->setCardNumber($_POST['card_number']);
            $card->setExpiryDate($_POST['expiry_date']);
            $card->setCvv($_POST['cvv']);
            $cardDao->update($card, $masterPassword);
        }
        header('Location: cartoes.php');
        exit();

    case 'get_details':
        header('Content-Type: application/json');
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID do cartão não fornecido.']);
            exit();
        }
        $cardData = $cardDao->getById((int)$_GET['id'], $userId);
        if ($cardData) {
            $decryptedData = [
                'success' => true,
                'id' => $cardData['id'],
                'card_name' => Crypto::decrypt($cardData['card_name'], $masterPassword),
                'card_holder_name' => Crypto::decrypt($cardData['card_holder_name'], $masterPassword),
                'card_number' => Crypto::decrypt($cardData['card_number'], $masterPassword),
                'expiry_date' => Crypto::decrypt($cardData['expiry_date'], $masterPassword),
                'cvv' => Crypto::decrypt($cardData['cvv'], $masterPassword),
            ];
            echo json_encode($decryptedData);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cartão não encontrado.']);
        }
        exit();

    case 'delete':
        if (isset($_GET['id'])) {
            $cardDao->delete((int)$_GET['id'], $userId);
        }
        header('Location: cartoes.php');
        exit();
}

header('Location: cartoes.php');
exit();