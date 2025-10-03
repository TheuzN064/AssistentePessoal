<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

// Validação de segurança aprimorada
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../dao/PersonDAO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/navbar.php'; // Inclui o template

$personDao = new PersonDAO();
$people = [];
$masterPassword = $_SESSION['master_password_session'] ?? null;

// Só busca as pessoas se o cofre estiver desbloqueado
if ($masterPassword) {
    $people = $personDao->getAllByUserId($_SESSION['user_id']);
}

// Inicia a renderização da página
$pageTitle = 'Gerenciar Pessoas';
render_header($pageTitle);
?>

<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">Gerenciar Pessoas</h1>
        <p class="text-gray-400 text-sm sm:text-base">Adicione pessoas para compartilhar assinaturas.</p>
    </div>
    <a href="assinaturas.php" class="px-4 py-2 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">Voltar</a>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md space-y-8">
    <?php if (!$masterPassword): ?>
        <div class="text-center p-4 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-md">
            <h3 class="font-bold">Cofre Bloqueado</h3>
            <p>Desbloqueie o cofre na <a href="senhas.php" class="text-blue-400 font-semibold hover:underline">página de senhas</a> para gerenciar as pessoas.</p>
        </div>
    <?php else: ?>
        <div>
            <h2 class="text-xl font-semibold mb-4 text-white">Adicionar Nova Pessoa</h2>
            <form action="processa_pessoa.php" method="POST" class="flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="action" value="create">
                <input type="text" name="name" placeholder="Nome da Pessoa" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
                <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md">Adicionar</button>
            </form>
        </div>

        <div>
            <h2 class="text-xl font-semibold mb-4 text-white">Pessoas Adicionadas</h2>
            <div class="space-y-2">
                <?php if (empty($people)): ?>
                    <p class="text-center text-gray-400 py-4">Nenhuma pessoa adicionada ainda.</p>
                <?php else: ?>
                    <?php foreach ($people as $person): ?>
                    <div class="flex items-center justify-between p-3 border border-gray-700 rounded-md bg-gray-900/50">
                        <span class="text-gray-300"><?= htmlspecialchars(Crypto::decrypt($person['name'], $masterPassword)) ?></span>
                        <a href="processa_pessoa.php?action=delete&id=<?= $person['id'] ?>" onclick="return confirm('Tem certeza?')" class="text-red-500 hover:text-red-400 text-sm">Apagar</a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Finaliza a renderização da página
render_footer();
?>