<?php
$pageTitle = 'Contas a Pagar';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);
require_once __DIR__ . '/../dao/BillDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');
$dateObj = DateTime::createFromFormat('!m', $month);
$monthName = $dateObj->format('F');

$billDao = new BillDAO();
$bills = [];
$totalUnpaid = 0;

if (isset($_SESSION['master_password_session'])) {
    $masterPassword = $_SESSION['master_password_session'];
    $bills = $billDao->getBillsByMonthYear($_SESSION['user_id'], $month, $year);
    foreach ($bills as $bill) {
        if (!$bill['is_paid']) {
            $decryptedValue = Crypto::decrypt($bill['value'], $masterPassword);
            $totalUnpaid += (float)$decryptedValue;
        }
    }
}
?>
<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Contas a Pagar</h1><p class="text-gray-400 text-sm sm:text-base">Gerencie suas contas mensais.</p></div>
    <div class="flex items-center gap-2 sm:gap-4">
        <button onclick="openModal('add-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2 text-xs sm:text-sm"><i class="ph-plus-circle"></i> Adicionar</button>
        <a href="dashboard.php" class="px-3 py-2 text-xs sm:text-sm bg-gray-700 hover:bg-gray-600 rounded-md">Voltar</a>
    </div>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <a href="?month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>" class="px-3 py-1 bg-gray-700 rounded-md">&lt;</a>
        <h2 class="text-xl font-semibold text-white"><?= $monthName . ' ' . $year ?></h2>
        <a href="?month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>" class="px-3 py-1 bg-gray-700 rounded-md">&gt;</a>
    </div>
    <div class="mb-4 p-4 bg-red-900/50 border border-red-700 rounded-md text-center">
        <p class="text-sm text-red-300">Total a Pagar este Mês</p>
        <p class="text-2xl font-bold text-white">R$ <?= number_format($totalUnpaid, 2, ',', '.') ?></p>
    </div>

    <?php if (!isset($_SESSION['master_password_session'])): ?>
        <p class="text-center text-gray-400">Desbloqueie o cofre na <a href="senhas.php" class="text-blue-400">página de senhas</a> para gerenciar as contas.</p>
    <?php elseif (empty($bills)): ?>
        <p class="text-center text-gray-400">Nenhuma conta para este mês.</p>
    <?php else: ?>
        <form action="processa_conta.php" method="POST">
            <input type="hidden" name="action" value="update_paid_statuses">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-lg mb-2">A Pagar</h3>
                    <?php foreach ($bills as $bill): if ($bill['is_paid']) continue; ?>
                    <div class="flex items-center justify-between p-3 bg-gray-900 border border-gray-700 rounded-md gap-2">
                        <div class="flex items-center gap-3 flex-grow">
                            <input type="checkbox" name="paid_bills_ids[]" value="<?= $bill['id'] ?>" class="h-5 w-5 text-green-500 bg-gray-700 border-gray-600 rounded flex-shrink-0">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars(Crypto::decrypt($bill['name'], $masterPassword)) ?></p>
                                <p class="text-xs text-gray-400">Vence em: <?= date('d/m/Y', strtotime($bill['due_date'])) ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <p class="font-semibold text-red-400">R$ <?= number_format((float)Crypto::decrypt($bill['value'], $masterPassword), 2, ',', '.') ?></p>
                            <a href="processa_conta.php?action=delete&id=<?= $bill['id'] ?>&month=<?= $month ?>&year=<?= $year ?>" 
                               onclick="return confirm('Tem certeza?')" class="text-gray-500 hover:text-red-400"><i class="ph-trash"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2">Pagas</h3>
                    <?php foreach ($bills as $bill): if (!$bill['is_paid']) continue; ?>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 border border-gray-700 rounded-md text-gray-500 gap-2">
                        <div class="flex items-center gap-3 flex-grow">
                            <input type="checkbox" name="paid_bills_ids[]" value="<?= $bill['id'] ?>" checked class="h-5 w-5 text-green-500 bg-gray-700 border-gray-600 rounded flex-shrink-0">
                            <div class="line-through">
                                <p class="font-medium"><?= htmlspecialchars(Crypto::decrypt($bill['name'], $masterPassword)) ?></p>
                                <p class="text-xs">Pago em: <?= date('d/m/Y', strtotime($bill['paid_at'])) ?></p>
                            </div>
                        </div>
                         <div class="flex items-center gap-4">
                            <p class="font-semibold text-green-400 line-through">R$ <?= number_format((float)Crypto::decrypt($bill['value'], $masterPassword), 2, ',', '.') ?></p>
                             <a href="processa_conta.php?action=delete&id=<?= $bill['id'] ?>&month=<?= $month ?>&year=<?= $year ?>"
                               onclick="return confirm('Tem certeza?')" class="text-gray-500 hover:text-red-400"><i class="ph-trash"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-4 py-2 font-semibold bg-green-600 text-white rounded-md hover:bg-green-700">Salvar Alterações</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<div id="add-modal" class="modal fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md">
        <form action="processa_conta.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Nova Conta</h2>
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <input type="text" name="name" placeholder="Nome da Conta (Ex: Energia)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" id="valor-conta" name="value" placeholder="Valor" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <input type="date" name="due_date" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('add-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script>
    // Inicia o modal como escondido
    document.getElementById('add-modal').classList.add('hidden');
    document.getElementById('add-modal').classList.remove('flex');

    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (document.getElementById('valor-conta')) {
        new Cleave('#valor-conta', {
            numeral: true,
            numeralDecimalMark: ',',
            delimiter: '.'
        });
    }
</script>
<?php render_footer(); ?>
