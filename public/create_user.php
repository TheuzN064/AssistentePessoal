<?php
// !! SCRIPT TEMPORÁRIO. DELETE APÓS O USO !!
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/ConexaoPDO.php';


// --- CONFIGURE SUAS DUAS SENHAS AQUI ---
$login_username = 'nygel';
$login_password = 'ninja06';   // Senha para entrar no site
$vault_password = '2006'; // Senha para descriptografar os dados

// Gera os hashes para ambas as senhas
$login_hashed = password_hash($login_password, PASSWORD_DEFAULT);
$vault_hashed = password_hash($vault_password, PASSWORD_DEFAULT);

echo "<p>Tentando criar o usuário: <strong>" . htmlspecialchars($login_username) . "</strong></p>";

try {
    $pdo = ConexaoPDO::getInstance();
    $sql = "INSERT INTO users (username, password_hash, vault_password_hash) VALUES (:username, :login_hash, :vault_hash)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'username' => $login_username,
        'login_hash' => $login_hashed,
        'vault_hash' => $vault_hashed
    ]);

    echo "<h1>SUCESSO!</h1>";
    echo "<p>Usuário criado.</p>";
    echo "<p>Use '<strong>".htmlspecialchars($login_username)."</strong>' e '<strong>".htmlspecialchars($login_password)."</strong>' para logar.</p>";
    echo "<p>Use '<strong>".htmlspecialchars($vault_password)."</strong>' para desbloquear o cofre de senhas.</p>";
    echo "<p style='color:red; font-weight:bold;'>AGORA DELETE ESTE ARQUIVO!</p>";

} catch (PDOException $e) {
    echo "<h1>ERRO!</h1>";
    if ($e->getCode() == 23000) {
        echo "<p>O usuário <strong>'" . htmlspecialchars($login_username) . "'</strong> já existe.</p>";
    } else {
        echo "<p>Mensagem: " . $e->getMessage() . "</p>";
    }
}
