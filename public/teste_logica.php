<?php
// /public/teste_logica.php

// Força a exibição de todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cabeçalho do Teste
echo "<!DOCTYPE html><html lang='pt-BR'><head><title>Diagnóstico da Lógica</title><style>body { font-family: monospace; padding: 20px; } .ok { color: green; font-weight: bold; } .erro { color: red; font-weight: bold; } pre { background-color: #eee; padding: 10px; border: 1px solid #ccc; }</style></head><body>";
echo "<h1>Diagnóstico da Lógica de Cadastro</h1><p>Este teste simula um cadastro completo, passo a passo.</p><hr>";

try {
    // PASSO 1: Carregar todas as dependências
    echo "<h2>Passo 1: Carregando Arquivos</h2>";
    require_once __DIR__ . '/../config/ConexaoPDO.php';
    echo "<p>Carregando ConexaoPDO.php... <span class='ok'>OK</span></p>";
    require_once __DIR__ . '/../security/Crypto.php';
    echo "<p>Carregando Crypto.php... <span class='ok'>OK</span></p>";
    require_once __DIR__ . '/../security/Logger.php';
    echo "<p>Carregando Logger.php... <span class='ok'>OK</span></p>";
    require_once __DIR__ . '/../models/Transaction.php';
    echo "<p>Carregando Transaction.php... <span class='ok'>OK</span></p>";
    require_once __DIR__ . '/../dao/TransactionDAO.php';
    echo "<p>Carregando TransactionDAO.php... <span class='ok'>OK</span></p>";
    echo "<hr>";

    // PASSO 2: Preparar dados de teste (simulando sessão e formulário)
    echo "<h2>Passo 2: Preparando Dados de Teste</h2>";
    $testUserId = 3; // Usando o ID do seu último teste
    $testMasterPassword = '1425'; // Usando a senha do seu último teste
    $testTransactionData = [
        'name' => 'Teste de Lógica Completa',
        'description' => 'Descrição de teste.',
        'value' => '99.99',
        'type' => 'income',
        'transaction_date' => date('Y-m-d'),
        'category_id' => 2
    ];
    echo "<p>Dados preparados com sucesso.</p>";
    echo "<pre>" . print_r($testTransactionData, true) . "</pre>";
    echo "<hr>";
    
    // PASSO 3: Criar objetos
    echo "<h2>Passo 3: Criando Objetos</h2>";
    $transaction = new Transaction();
    $transaction->setUserId($testUserId);
    $transaction->setName($testTransactionData['name']);
    $transaction->setDescription($testTransactionData['description']);
    $transaction->setValue($testTransactionData['value']);
    $transaction->setType($testTransactionData['type']);
    $transaction->setTransactionDate($testTransactionData['transaction_date']);
    $transaction->setCategoryId($testTransactionData['category_id']);
    echo "<p>Objeto 'Transaction' criado com sucesso.</p>";
    
    $dao = new TransactionDAO();
    echo "<p>Objeto 'TransactionDAO' criado com sucesso.</p>";
    echo "<hr>";

    // PASSO 4: Executar a lógica de negócio (criptografar e salvar)
    echo "<h2>Passo 4: Executando DAO->create()</h2>";
    echo "<p>Tentando criptografar e inserir os dados no banco...</p>";
    
    $result = $dao->create($transaction, $testMasterPassword);
    
    echo "<h3>Resultado do DAO->create():</h3>";
    echo "<pre>";
    var_dump($result);
    echo "</pre>";

    if ($result === true) {
        echo "<h2 style='color: green;'>SUCESSO FINAL!</h2>";
        echo "<p>A lógica de cadastro, incluindo a criptografia, funcionou e o DAO retornou 'true'.</p>";
    } else {
        echo "<h2 style='color: red;'>FALHA NA LÓGICA!</h2>";
        echo "<p>O método DAO->create() foi executado, mas retornou um erro. A mensagem de erro específica está acima.</p>";
    }

} catch (Throwable $e) {
    echo "<h2 style='color: red;'>ERRO FATAL DURANTE O TESTE!</h2>";
    echo "<p>O script parou inesperadamente. A causa do erro está abaixo:</p>";
    echo "<pre style='background-color: #ffecec; border: 1px solid red; padding: 10px;'>";
    echo "<strong>Tipo de Erro:</strong> " . get_class($e) . "\n";
    echo "<strong>Mensagem:</strong> " . $e->getMessage() . "\n";
    echo "<strong>Arquivo:</strong> " . $e->getFile() . "\n";
    echo "<strong>Linha:</strong> " . $e->getLine() . "\n";
    echo "</pre>";
}

echo "</body></html>";
exit();
?>
