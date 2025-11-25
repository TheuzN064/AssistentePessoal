<?php
$pageTitle = 'Meus Cartões';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

require_once __DIR__ . '/../dao/CardDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

$cardDao = new CardDAO();
$cards = [];
$masterPassword = $_SESSION['master_password_session'] ?? null;

if ($masterPassword) {
    $cards = $cardDao->getAllByUserId($_SESSION['user_id']);
}
?>

<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Meus Cartões</h1><p class="text-gray-400 text-sm sm:text-base">Gerencie seus cartões de crédito e débito.</p></div>
    <div class="flex items-center gap-2 sm:gap-4">
        <button onclick="openModal('add-card-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2 text-xs sm:text-sm"><i class="ph-plus-circle"></i> Adicionar</button>
        <a href="dashboard.php" class="px-3 py-2 text-xs sm:text-sm bg-gray-700 hover:bg-gray-600 rounded-md">Voltar</a>
    </div>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <?php if (!$masterPassword): ?>
        <div class="text-center p-4 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-md">
            <h3 class="font-bold">Cofre Bloqueado</h3>
            <p>Desbloqueie o cofre na <a href="senhas.php" class="text-blue-400 font-semibold hover:underline">página de senhas</a> para gerenciar os cartões.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <button onclick="openModal('add-card-modal')" class="flex flex-col items-center justify-center p-6 bg-gray-900/50 border-2 border-dashed border-gray-700 rounded-lg hover:bg-gray-700 hover:border-blue-500 transition-colors h-52">
                <i class="ph-plus-circle text-5xl text-gray-600"></i>
                <span class="mt-2 font-semibold text-gray-500">Adicionar Cartão</span>
            </button>

            <?php foreach ($cards as $card): 
                $decryptedCardNumber = Crypto::decrypt($card['card_number'], $masterPassword);
                $lastFourDigits = substr($decryptedCardNumber, -4);
                $cardType = 'default';
                if (strpos($decryptedCardNumber, '4') === 0) $cardType = 'visa';
                elseif (strpos($decryptedCardNumber, '5') === 0) $cardType = 'mastercard';
            ?>
                <div class="relative p-6 bg-gradient-to-br from-gray-700 to-gray-800 rounded-lg shadow-lg flex flex-col justify-between h-52 text-white">
                    
                    <div class="absolute top-3 left-3 flex gap-1.5">
                        <a href="processa_cartao.php?action=delete&id=<?= $card['id'] ?>" onclick="return confirm('Tem certeza que deseja apagar este cartão?')" class="block w-3 h-3 bg-red-500 rounded-full hover:bg-red-400" title="Apagar"></a>
                        <button onclick="openEditModal(<?= $card['id'] ?>)" class="block w-3 h-3 bg-blue-500 rounded-full hover:bg-blue-400" title="Editar"></button>
                    </div>

                    <div class="flex justify-between items-start pt-4">
                        <span class="font-bold text-lg"><?= htmlspecialchars(Crypto::decrypt($card['card_name'], $masterPassword)) ?></span>
                        <img src="https://img.icons8.com/color/48/000000/<?= $cardType ?>.png" alt="<?= $cardType ?>" class="w-12"/>
                    </div>
                    <div class="text-center font-mono text-xl tracking-widest">
                        •••• •••• •••• <?= $lastFourDigits ?>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-sm"><?= htmlspecialchars(Crypto::decrypt($card['card_holder_name'], $masterPassword)) ?></span>
                        <span class="text-sm">Exp: <?= htmlspecialchars(Crypto::decrypt($card['expiry_date'], $masterPassword)) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="add-card-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md">
        <form action="processa_cartao.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Adicionar Novo Cartão</h2>
            <input type="hidden" name="action" value="create">
            <input type="text" name="card_name" placeholder="Apelido do Cartão (Ex: Banco X)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <input type="text" name="card_holder_name" placeholder="Nome no Cartão" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <div class="grid grid-cols-3 gap-4">
                <input type="text" name="card_number" placeholder="Número do Cartão" required class="col-span-3 w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <input type="text" name="expiry_date" placeholder="Validade (MM/AA)" required class="col-span-2 w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <input type="text" name="cvv" placeholder="CVV" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('add-card-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-card-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md">
        <form action="processa_cartao.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Editar Cartão</h2>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-card-id">
            <input type="text" name="card_name" id="edit-card-name" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white" placeholder="Apelido do Cartão">
            <input type="text" name="card_holder_name" id="edit-card-holder" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white" placeholder="Nome no Cartão">
             <div class="grid grid-cols-3 gap-4">
                <input type="text" name="card_number" id="edit-card-number" required class="col-span-3 w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white" placeholder="Número do Cartão">
                <input type="text" name="expiry_date" id="edit-card-expiry" required class="col-span-2 w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white" placeholder="Validade (MM/AA)">
                <input type="text" name="cvv" id="edit-card-cvv" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white" placeholder="CVV">
            </div>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('edit-card-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    async function openEditModal(cardId) {
        // Mostra o modal imediatamente com placeholders
        openModal('edit-card-modal');
        document.getElementById('edit-card-name').value = 'Carregando...';
        document.getElementById('edit-card-holder').value = 'Carregando...';
        document.getElementById('edit-card-number').value = 'Carregando...';
        document.getElementById('edit-card-expiry').value = 'Carregando...';
        document.getElementById('edit-card-cvv').value = '***';

        // Busca os dados já prontos do servidor (armazenados em texto simples)
        try {
            const response = await fetch(`processa_cartao.php?action=get_details&id=${cardId}`);
            if (!response.ok) {
                throw new Error('Falha na comunicação com o servidor.');
            }
            const data = await response.json();

            if (data.success) {
                // Preenche o formulário com os dados recebidos
                document.getElementById('edit-card-id').value = data.id;
                document.getElementById('edit-card-name').value = data.card_name;
                document.getElementById('edit-card-holder').value = data.card_holder_name;
                document.getElementById('edit-card-number').value = data.card_number;
                document.getElementById('edit-card-expiry').value = data.expiry_date;
                document.getElementById('edit-card-cvv').value = data.cvv;
            } else {
                alert('Erro: ' + data.message);
                closeModal('edit-card-modal');

            }
        } catch (error) {
            alert('Ocorreu um erro ao buscar os dados do cartão.');
            closeModal('edit-card-modal');
        }
    }
</script>

<?php render_footer(); ?>