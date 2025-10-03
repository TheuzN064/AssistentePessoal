<?php
require_once __DIR__ . '/../security/Logger.php';
require_once __DIR__ . '/../security/ContactEmailHelper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = $_POST['nome'] ?? 'Anônimo';
    $email   = $_POST['email'] ?? 'sem-email@dominio.com';
    $assunto = $_POST['assunto'] ?? 'Contato';
    $mensagem= $_POST['mensagem'] ?? '';

    if (ContactEmailHelper::sendContact($email, $nome, $assunto, $mensagem)) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Falha ao enviar mensagem.";
    }
}
