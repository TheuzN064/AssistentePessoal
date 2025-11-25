<?php
// /public/verifica_vazamentos.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

// Segurança: Garante que o usuário está logado E o cofre está desbloqueado.
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['master_password_session'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado ou cofre bloqueado.']);
    exit();
}

require_once __DIR__ . '/../dao/PasswordDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

header('Content-Type: application/json');
$masterPassword = $_SESSION['master_password_session'];
$userId = $_SESSION['user_id'];
$passwordDao = new PasswordDAO();
$pwnedPasswords = [];

try {
    // 1. Busca todas as senhas salvas do usuário (armazenadas em texto simples neste modo)
    $allPasswordsEncrypted = $passwordDao->getAllDetailsByUserId($userId);
    
    foreach ($allPasswordsEncrypted as $p) {
        // 2. Lê a senha como está salva em texto simples
        $decryptedPassword = Crypto::decrypt($p['password'], $masterPassword);
        
        if (empty($decryptedPassword)) {
            continue;
        }

        // 3. Prepara a senha para a API do HIBP (k-Anonymity)
        $sha1Hash = strtoupper(sha1($decryptedPassword));
        $hashPrefix = substr($sha1Hash, 0, 5);
        $hashSuffix = substr($sha1Hash, 5);

        // 4. Faz a chamada para a API do HIBP
        // Usamos cURL para melhor controle e desempenho
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.pwnedpasswords.com/range/" . $hashPrefix);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AssisPessoal-App'); // Boa prática
        $response = curl_exec($ch);
        curl_close($ch);

        // 5. Verifica se o hash da nossa senha está na resposta da API
        if ($response) {
            $hashes = explode("\n", $response);
            foreach ($hashes as $hashLine) {
                list($suffix, $count) = explode(':', $hashLine);
                if (trim($suffix) === $hashSuffix) {
                    $pwnedPasswords[] = [
                        'id' => $p['id'],
                        'name' => Crypto::decrypt($p['name'], $masterPassword),
                        'count' => (int)$count
                    ];
                    break; // Encontrou, pode parar de procurar para esta senha
                }
            }
        }
    }

    echo json_encode(['success' => true, 'pwned' => $pwnedPasswords]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro interno durante a verificação.']);
}

exit();