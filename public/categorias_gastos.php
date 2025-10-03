<?php
require_once __DIR__ . '/../vendor/autoload.php';

// ADICIONADO: Inclusão manual do DAO para corrigir o erro "Class not found".
require_once __DIR__ . '/../dao/TransactionCategoryDAO.php';

$pageTitle = 'Categorias de Gastos';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

$categoryDAO = new TransactionCategoryDAO();
$categories = $categoryDAO->getAllByUserId($_SESSION['user_id']);
?>

<header class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Categorias de Gastos</h1><p class="text-gray-400 text-sm sm:text-base">Organize suas despesas.</p></div>
    <a href="gastos.php" class="px-4 py-2 text-sm bg-gray-700 text-gray-200 hover:bg-gray-600 rounded-md">Voltar</a>
</header>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-gray-800 border border-gray-700 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4 text-white">Nova Categoria</h2>
        <form action="processa_categoria.php" method="POST" class="flex gap-2">
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" placeholder="Nome (Ex: Lazer, Moradia)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md">Criar</button>
        </form>
    </div>
    <div class="bg-gray-800 border border-gray-700 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4 text-white">Categorias Existentes</h2>
        <div class="space-y-2">
            <?php if(empty($categories)): ?>
                <p class="text-center text-gray-500">Nenhuma categoria criada.</p>
            <?php else: ?>
                <?php foreach($categories as $cat): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 border border-gray-700 rounded-md">
                        <span class="text-gray-300"><?= htmlspecialchars($cat['name']) ?></span>
                        <a href="processa_categoria.php?action=delete&id=<?= $cat['id'] ?>" onclick="return confirm('Tem certeza?')" class="text-gray-500 hover:text-red-400"><i class="ph ph-trash"></i></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php render_footer(); ?>