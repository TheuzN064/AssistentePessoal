<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dao/TransactionDAO.php';
require_once __DIR__ . '/../dao/TransactionCategoryDAO.php';

$pageTitle = 'Controle de Gastos';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

$transactionDAO = new TransactionDAO();
$categoryDAO = new TransactionCategoryDAO();

$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt', 'portuguese');
$dateObj = DateTime::createFromFormat('!m', $month);
$monthName = strftime('%B', $dateObj->getTimestamp());

$transactions = [];
$categories = [];
$summary = ['income' => 0.0, 'expense' => 0.0, 'balance' => 0.0];
$masterPassword = $_SESSION['master_password_session'] ?? null;

$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);
$formSuccess = $_SESSION['form_success'] ?? null;
unset($_SESSION['form_success']);

if ($masterPassword) {
    $summary = $transactionDAO->getMonthlySummary($_SESSION['user_id'], $month, $year, $masterPassword);
    $transactions = $transactionDAO->getByMonthYear($_SESSION['user_id'], $month, $year, $masterPassword);
    $categories = $categoryDAO->getAllByUserId($_SESSION['user_id']);
}
?>
<header class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">Controle de Gastos</h1>
        <p class="text-gray-400 text-sm sm:text-base">Acompanhe suas receitas e despesas.</p>
    </div>
    <div class="flex items-center gap-2 sm:gap-4">
        <a href="categorias_gastos.php" class="px-3 py-2 text-xs sm:text-sm bg-gray-700 rounded-md">Categorias</a>
        <button onclick="openModal('add-transaction-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2 text-xs sm:text-sm"><i class="ph-plus-circle"></i> Adicionar</button>
    </div>
</header>

<?php if ($formError): ?>
    <div class="p-4 mb-4 text-sm font-bold bg-red-900/50 border-2 border-red-700 text-red-300 rounded-lg shadow-lg">
        <strong>ERRO:</strong> <?= htmlspecialchars($formError) ?>
    </div>
<?php endif; ?>
<?php if ($formSuccess): ?>
    <div class="p-4 mb-4 text-sm font-bold bg-green-900/50 border-2 border-green-700 text-green-300 rounded-lg shadow-lg">
        <strong>SUCESSO:</strong> <?= htmlspecialchars($formSuccess) ?>
    </div>
<?php endif; ?>

<?php if (!$masterPassword): ?>
    <div class="text-center p-6 bg-gray-800 border border-gray-700 rounded-lg">
        <p class="text-gray-400">Desbloqueie o cofre no <a href="dashboard.php" class="text-blue-400 font-semibold">Dashboard</a> para gerenciar seus gastos.</p>
    </div>
<?php else: ?>
    <div class="mb-6 p-4 bg-gray-800 border border-gray-700 rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <a href="?month=<?= $month == 1 ? 12 : $month - 1 ?>&year=<?= $month == 1 ? $year - 1 : $year ?>" class="px-3 py-1 bg-gray-700 rounded-md hover:bg-gray-600">&lt;</a>
            <h2 class="text-xl font-semibold text-white capitalize"><?= $monthName . ' ' . $year ?></h2>
            <a href="?month=<?= $month == 12 ? 1 : $month + 1 ?>&year=<?= $month == 12 ? $year + 1 : $year ?>" class="px-3 py-1 bg-gray-700 rounded-md hover:bg-gray-600">&gt;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="bg-green-900/50 p-3 rounded-md">
                <p class="text-sm text-green-300">Receitas no Mês</p>
                <p class="text-2xl font-bold text-white">R$ <?= number_format($summary['income'], 2, ',', '.') ?></p>
            </div>
            <div class="bg-red-900/50 p-3 rounded-md">
                <p class="text-sm text-red-300">Despesas no Mês</p>
                <p class="text-2xl font-bold text-white">R$ <?= number_format($summary['expense'], 2, ',', '.') ?></p>
            </div>
            <div class="bg-blue-900/50 p-3 rounded-md">
                <p class="text-sm text-blue-300">Saldo Atualizado</p>
                <p class="text-2xl font-bold text-white">R$ <?= number_format($summary['balance'], 2, ',', '.') ?></p>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-white">Lançamentos do Mês</h2>
        <div class="space-y-3">
            <?php if (empty($transactions)): ?>
                <p class="text-center text-gray-500 py-4">Nenhum lançamento para este mês.</p>
            <?php else: ?>
                <?php foreach ($transactions as $t): ?>
                    <div class="flex items-center p-3 bg-gray-900/50 border border-gray-700 rounded-md">
                        <div class="mr-3">
                            <?php if ($t['type'] === 'income'): ?>
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500/20 text-green-400"><i class="ph-fill ph-arrow-fat-up"></i></span>
                            <?php else: ?>
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500/20 text-red-400"><i class="ph-fill ph-arrow-fat-down"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow">
                            <p class="font-semibold text-white"><?= htmlspecialchars($t['name']) ?></p>
                            <p class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($t['transaction_date'])) ?> <?= $t['category_name'] ? ' &bull; ' . htmlspecialchars($t['category_name']) : '' ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold <?= $t['type'] === 'income' ? 'text-green-400' : 'text-red-400' ?>">
                                <?= ($t['type'] === 'income' ? '+' : '-') ?> R$ <?= number_format($t['value'], 2, ',', '.') ?>
                            </p>
                            <a href="processa_transacao.php?action=delete&id=<?= $t['id'] ?>&month=<?= $month ?>&year=<?= $year ?>" onclick="return confirm('Tem certeza?')" class="text-xs text-gray-500 hover:text-red-400">Apagar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div id="add-transaction-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4 z-50">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-lg">
        <form action="processa_transacao.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Novo Lançamento</h2>
            
            <input type="hidden" name="action" value="create">

            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <div class="flex gap-4 mb-4">
                <label class="flex-1"><input type="radio" name="type" value="expense" checked onchange="toggleCategoryField()" class="hidden peer"><span class="type-btn peer-checked:bg-red-600 peer-checked:text-white">Despesa</span></label>
                <label class="flex-1"><input type="radio" name="type" value="income" onchange="toggleCategoryField()" class="hidden peer"><span class="type-btn peer-checked:bg-green-600 peer-checked:text-white">Receita</span></label>
            </div>
            <input type="text" name="name" placeholder="Nome (Ex: Salário, Aluguel)" required class="input-field">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" id="valor-transacao" name="value" placeholder="Valor (R$)" required class="input-field">
                <input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required class="input-field">
            </div>
            <div id="category-field">
                <select name="category_id" class="input-field">
                    <option value="">Selecione uma categoria...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <textarea name="description" placeholder="Descrição (opcional)" rows="2" class="input-field"></textarea>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('add-transaction-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<style>
    .input-field { width: 100%; padding-left: 0.75rem; padding-right: 0.75rem; padding-top: 0.5rem; padding-bottom: 0.5rem; background-color: #374151; border: 1px solid #4b5563; border-radius: 0.375rem; color: white; }
    .type-btn { display: block; width: 100%; text-align: center; padding: 0.5rem; border-radius: 0.375rem; background-color: #4b5563; cursor: pointer; }
</style>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
    function toggleCategoryField() {
        const type = document.querySelector('input[name="type"]:checked').value;
        document.getElementById('category-field').style.display = (type === 'expense') ? 'block' : 'none';
    }
    if (document.getElementById('valor-transacao')) {
        new Cleave('#valor-transacao', { numeral: true, numeralDecimalMark: ',', delimiter: '.' });
    }
</script>
<?php render_footer(); ?>