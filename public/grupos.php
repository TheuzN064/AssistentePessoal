<?php
$pageTitle = 'Gerenciar Grupos';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);
require_once __DIR__ . '/../dao/GroupDAO.php';
$groupDao = new GroupDAO();
$groups = $groupDao->getAllByUserId($_SESSION['user_id']);
?>
<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Gerenciar Grupos</h1><p class="text-gray-400 text-sm sm:text-base">Crie, edite ou apague seus grupos de senhas.</p></div>
    <a href="senhas.php" class="px-4 py-2 text-sm bg-gray-700 text-gray-200 hover:bg-gray-600 rounded-md">Voltar para Senhas</a>
</header>
<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md space-y-6">
    <!-- Seção para Adicionar Novo Grupo -->
    <div>
        <h2 class="text-xl font-semibold mb-4 text-white">Adicionar Novo Grupo</h2>
        <form action="processa_grupo.php" method="POST" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" placeholder="Nome do novo grupo" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md">Criar Grupo</button>
        </form>
    </div>

    <!-- Seção de Grupos Existentes -->
    <div>
        <h2 class="text-xl font-semibold mb-4 text-white">Grupos Existentes</h2>
        <div class="space-y-2">
            <?php if (empty($groups)): ?>
                <p class="text-center text-gray-400">Nenhum grupo criado ainda.</p>
            <?php else: ?>
                <?php foreach ($groups as $group): ?>
                <div class="flex items-center justify-between p-3 border border-gray-700 rounded-md bg-gray-900/50">
                    <div class="flex-grow">
                        <!-- Nome do grupo (visível por padrão) -->
                        <span id="group-name-<?= $group['id'] ?>" class="text-gray-300 cursor-pointer" onclick="toggleEdit(<?= $group['id'] ?>)"><?= htmlspecialchars($group['name']) ?></span>
                        <!-- Formulário de edição (oculto por padrão) -->
                        <form id="edit-form-<?= $group['id'] ?>" action="processa_grupo.php" method="POST" class="hidden flex-grow flex items-center gap-2">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $group['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($group['name']) ?>" class="w-full px-2 py-1 bg-gray-700 border border-gray-600 rounded-md text-white">
                            <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Salvar</button>
                        </form>
                    </div>
                    <div class="flex items-center ml-4">
                         <button id="edit-btn-<?= $group['id'] ?>" onclick="toggleEdit(<?= $group['id'] ?>)" class="px-3 py-1 text-sm text-blue-400 hover:text-blue-300">Editar</button>
                         <a href="processa_grupo.php?action=delete&id=<?= $group['id'] ?>" onclick="return confirm('Tem certeza? Apagar um grupo não apaga as senhas dentro dele.')" class="ml-2 text-red-500 hover:text-red-400 text-sm">Apagar</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleEdit(id) {
        const nameSpan = document.getElementById(`group-name-${id}`);
        const editForm = document.getElementById(`edit-form-${id}`);
        const editBtn = document.getElementById(`edit-btn-${id}`);

        nameSpan.classList.toggle('hidden');
        editForm.classList.toggle('hidden');
        
        if (editForm.classList.contains('hidden')) {
            editBtn.innerText = 'Editar';
        } else {
            editBtn.innerText = 'Cancelar';
        }
    }
</script>
<?php render_footer(); ?>
