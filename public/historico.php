<?php
require_once __DIR__ . '/../security/SessionManager.php';
SessionManager::checkActivity();

// Validação de segurança: Garante que o usuário está logado E o cofre está desbloqueado.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['master_password_session'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../dao/SubscriptionDAO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/navbar.php'; // Inclui o template

$subscriptionId = $_GET['id'] ?? 0;
if (!$subscriptionId) {
    header('Location: assinaturas.php');
    exit;
}

$masterPassword = $_SESSION['master_password_session'];
$subDao = new SubscriptionDAO();

$subscription = $subDao->getSubscriptionById($subscriptionId, $_SESSION['user_id']);
$subscriptionName = $subscription ? Crypto::decrypt($subscription['name'], $masterPassword) : 'Desconhecida';

$history = $subDao->getPaymentHistory($subscriptionId, $_SESSION['user_id']);

// Inicia a renderização da página usando o layout padrão
$pageTitle = 'Histórico de Pagamentos';
render_header($pageTitle);
?>

<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">Extrato de Pagamentos</h1>
        <p class="text-gray-400 text-sm sm:text-base">Histórico para: <?= htmlspecialchars($subscriptionName) ?></p>
    </div>
    <a href="assinaturas.php" class="px-4 py-2 text-sm bg-gray-700 hover:bg-gray-600 rounded-md">Voltar</a>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md space-y-6">
    <?php if (empty($history)): ?>
        <p class="text-center text-gray-400">Nenhum pagamento registrado para esta assinatura.</p>
    <?php else: ?>
        <?php foreach ($history as $monthYear => $people_statuses): ?>
            <div>
                <h3 class="font-semibold text-lg text-white mb-2"><?= htmlspecialchars($monthYear) ?></h3>
                <div class="space-y-2">
                    <?php foreach ($people_statuses as $status): ?>
                        <div class="flex items-center p-2 bg-gray-900/50 rounded-md">
                            <span class="mr-3 text-xl"><?= $status['paid'] ? '✅' : '❌' ?></span>
                            <span class="text-gray-300"><?= htmlspecialchars(Crypto::decrypt($status['name'], $masterPassword)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// Finaliza a renderização da página
render_footer();
?>