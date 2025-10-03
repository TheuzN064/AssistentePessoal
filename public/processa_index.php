<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$statusMessage = '';
$statusClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . '/../security/Logger.php';
    require_once __DIR__ . '/../security/ContactEmailHelper.php';

    $nome = trim($_POST["nome"]);
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $mensagem = trim($_POST["mensagem"]);

    if (empty($nome) || !$email || empty($mensagem)) {
        $statusMessage = 'Por favor, preencha todos os campos corretamente.';
        $statusClass = 'erro';
    } else {
        if (ContactEmailHelper::sendContact($email, $nome, 'Contato do site', $mensagem)) {
            $statusMessage = 'Obrigado! Sua mensagem foi enviada com sucesso.';
            $statusClass = 'sucesso';
        } else {
            $statusMessage = 'Erro ao enviar mensagem. Tente novamente mais tarde.';
            $statusClass = 'erro';
        }
    }
}
?>
