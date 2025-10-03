<?php
// Garante que o autoloader do Composer seja o primeiro a ser carregado.
require_once __DIR__ . '/../vendor/autoload.php';

// Inclusão manual dos DAOs para corrigir qualquer erro "Class not found".
require_once __DIR__ . '/../dao/PasswordDAO.php';
require_once __DIR__ . '/../dao/GroupDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

$pageTitle = 'Gerenciador de Senhas';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

// O restante do script continua como planejado.
$passwordDao = new PasswordDAO();
$groupDao = new GroupDAO();

$allPasswords = [];
$groups = [];
$finalHighlightId = $_SESSION['pending_highlight_id'] ?? null;
unset($_SESSION['pending_highlight_id']);
$masterPassword = $_SESSION['master_password_session'] ?? null;
$vaultError = $_SESSION['vault_error'] ?? null;
unset($_SESSION['vault_error']);

if ($masterPassword) {
    $allPasswordsEncrypted = $passwordDao->getAllDetailsByUserId($_SESSION['user_id']);
    
    foreach($allPasswordsEncrypted as $p) {
        try {
            // Tenta descriptografar cada campo. Throwable pega qualquer tipo de erro.
            $decryptedPassword = [
                'id' => $p['id'],
                'group_id' => $p['group_id'],
                'group_name' => $p['group_name'] ?? 'Geral',
                'name' => Crypto::decrypt($p['name'], $masterPassword),
                'email' => Crypto::decrypt($p['email'], $masterPassword),
                'password' => Crypto::decrypt($p['password'], $masterPassword),
                'site_url' => Crypto::decrypt($p['site_url'], $masterPassword),
                'description' => Crypto::decrypt($p['description'], $masterPassword),
                'recovery_codes' => Crypto::decrypt($p['recovery_codes'], $masterPassword),
            ];
            $allPasswords[] = $decryptedPassword;
        } catch (Throwable $e) {
            // Se ocorrer um erro, este registro será ignorado, e a página não vai quebrar.
            continue;
        }
    }

    $groups = $groupDao->getAllByUserId($_SESSION['user_id']);
}
?>
<script src="https://cdn.jsdelivr.net/npm/otpauth@9.2.2/dist/otpauth.umd.min.js"></script>
<header class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Gerenciador de Senhas</h1><p class="text-gray-400 text-sm sm:text-base">Suas senhas, criptografadas e seguras.</p></div>
    <div class="flex items-center gap-2 sm:gap-4">
        <button onclick="openModal('add-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2 text-xs sm:text-sm"><i class="ph-plus-circle"></i> Adicionar</button>
        <a href="dashboard.php" class="px-3 py-2 text-xs sm:text-sm bg-gray-700 text-gray-200 hover:bg-gray-600 rounded-md">Voltar</a>
    </div>
</header>

<?php if (!$masterPassword): ?>
    <div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
        <div class="p-4 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-md">
            <h3 class="font-bold">Cofre Bloqueado</h3>
            <p>Digite sua senha do cofre para ver suas senhas.</p>
            <form method="POST" action="processa_senha.php" class="mt-4 flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="action" value="unlock_vault">
                <input type="password" name="master_password_for_session" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white" placeholder="Senha do Cofre" required>
                <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white rounded-md">Desbloquear</button>
            </form>
            <?php if ($vaultError): ?><p class="text-red-400 text-sm mt-2 font-semibold"><?= htmlspecialchars($vaultError) ?></p><?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-1 bg-gray-800 border border-gray-700 p-4 rounded-lg self-start">
            <h3 class="font-bold text-white mb-3 px-2">Grupos</h3>
            <ul class="space-y-1" id="groups-list">
                <li><a href="#" data-group-id="all" class="group-filter active-group flex items-center gap-2 px-2 py-1.5 rounded-md font-semibold">Todos</a></li>
                <li><a href="#" data-group-id="0" class="group-filter flex items-center gap-2 px-2 py-1.5 rounded-md">Geral</a></li>
                <?php foreach($groups as $group): ?>
                    <li><a href="#" data-group-id="<?= $group['id'] ?>" class="group-filter flex items-center gap-2 px-2 py-1.5 rounded-md"><?= htmlspecialchars($group['name']) ?></a></li>
                <?php endforeach; ?>
                 <li><a href="grupos.php" class="flex items-center gap-2 px-2 py-1.5 rounded-md text-sm text-blue-400 hover:bg-gray-700/50 mt-2"><i class="ph ph-pencil-simple"></i> Gerenciar Grupos</a></li>
            </ul>
        </div>
        <div class="md:col-span-3 space-y-4">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Buscar por nome ou e-mail..." class="w-full bg-gray-800 border border-gray-700 rounded-lg py-2 pl-10 pr-4 text-white">
                <i class="ph ph-magnifying-glass absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"></i>
            </div>
            <div id="passwords-list" class="space-y-2">
                <?php if (empty($allPasswords)): ?>
                    <p class="text-center text-gray-400 py-8">Nenhuma senha salva ainda.</p>
                <?php else: ?>
                    <?php foreach ($allPasswords as $pass): ?>
                        <div class="password-card bg-gray-800 border border-gray-700 rounded-lg" 
                             data-id="<?= $pass['id'] ?>"
                             data-group-id="<?= $pass['group_id'] ?? '0' ?>" 
                             data-search-term="<?= strtolower(htmlspecialchars($pass['name']).' '.htmlspecialchars($pass['email'])) ?>">
                            <div class="p-4 flex items-center gap-4 cursor-pointer" onclick="toggleDetails(this)">
                                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center font-bold text-xl text-white flex-shrink-0">
                                    <?= strtoupper(mb_substr($pass['name'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow overflow-hidden">
                                    <h4 class="font-bold text-white truncate"><?= htmlspecialchars($pass['name']) ?></h4>
                                    <p class="text-sm text-gray-400 truncate"><?= htmlspecialchars($pass['email']) ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick="event.stopPropagation(); copyToClipboard('<?= htmlspecialchars($pass['email'], ENT_QUOTES) ?>', this)" class="action-btn" title="Copiar Usuário"><i class="ph ph-user"></i></button>
                                    <button onclick="event.stopPropagation(); copyToClipboard('<?= htmlspecialchars($pass['password'], ENT_QUOTES) ?>', this)" class="action-btn" title="Copiar Senha"><i class="ph ph-key"></i></button>
                                </div>
                            </div>
                            <div class="details-content hidden border-t border-gray-700 p-4 space-y-3 text-sm">
                                <p><strong>Site:</strong> <a href="<?= htmlspecialchars($pass['site_url']) ?>" target="_blank" class="text-blue-400 hover:underline"><?= htmlspecialchars($pass['site_url']) ?></a></p>
                                <p><strong>Grupo:</strong> <?= htmlspecialchars($pass['group_name']) ?></p>
                                <p><strong>Descrição:</strong> <span class="block bg-gray-900/50 p-2 rounded-md mt-1 whitespace-pre-wrap"><?= htmlspecialchars($pass['description']) ?></span></p>
                                <div class="flex justify-end gap-2 pt-2">
                                    <button onclick="openEditModal(<?= $pass['id'] ?>)" class="text-xs font-semibold bg-blue-600/50 text-blue-300 px-2 py-1 rounded-md hover:bg-blue-600">Editar</button>
                                    <a href="processa_senha.php?action=delete&id=<?= $pass['id'] ?>" onclick="return confirm('Tem certeza?')" class="text-xs font-semibold bg-red-600/50 text-red-300 px-2 py-1 rounded-md hover:bg-red-600">Apagar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div id="no-results" class="hidden text-center text-gray-500 py-8">
                    <p>Nenhuma senha encontrada para sua busca.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div id="add-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4 z-50">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-lg">
        <form action="processa_senha.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Adicionar Nova Senha</h2>
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" id="name-add" placeholder="Nome (Ex: Conta Pessoal)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-400">Grupo Existente</label>
                    <select name="group_id" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                        <option value="">Nenhum</option>
                        <?php foreach($groups as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-400">Ou Novo Grupo</label>
                    <input type="text" name="new_group_name" placeholder="Criar novo grupo" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                </div>
            </div>
            <input type="url" name="site_url" placeholder="Site" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <input type="text" name="email" placeholder="Email / Usuário" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <div class="relative">
                <input type="text" name="password_text" id="password_text-add" placeholder="Senha" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white pr-20">
                <button type="button" onclick="generatePassword('add')" class="absolute inset-y-0 right-0 px-3 text-sm font-semibold text-blue-400 hover:text-blue-300">Gerar</button>
            </div>
            <textarea name="description" placeholder="Descrição..." rows="2" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white"></textarea>
            <div class="flex items-center">
                <input id="recovery-toggle-add" onchange="toggleRecovery('add')" type="checkbox" class="h-4 w-4 text-blue-500 bg-gray-700 border-gray-600 rounded">
                <label for="recovery-toggle-add" class="ml-2 block text-sm text-gray-300">Adicionar códigos de recuperação</label>
            </div>
            <div id="recovery-container-add" class="hidden">
                <textarea name="recovery_codes" placeholder="Cole os códigos aqui, um por linha..." rows="4" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white"></textarea>
            </div>
            <div class="flex justify-end gap-4 pt-2">
                <button type="button" onclick="closeModal('add-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>
<div id="edit-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4 z-50">
    <div id="edit-modal-content" class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-lg"></div>
</div>

<style>
    .active-group { background-color: #2563eb; color: white; }
    .action-btn { background-color: #374151; color: #9ca3af; padding: 0.5rem; border-radius: 9999px; transition: all 0.2s; }
    .action-btn:hover { background-color: #4b5563; color: white; }
</style>

<script>
    let activeGroupId = 'all';
    let passwordCards = [];
    let searchInput = null;
    let noResultsMessage = null;

    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('passwords-list')) return;

        passwordCards = document.querySelectorAll('.password-card');
        searchInput = document.getElementById('search-input');
        noResultsMessage = document.getElementById('no-results');
        
        addFilterListeners();
        searchInput.addEventListener('input', filterPasswords);

        const pwnedData = sessionStorage.getItem('pwnedPasswordsResult');
        if (pwnedData) {
            try {
                const pwnedPasswords = JSON.parse(pwnedData);
                if (pwnedPasswords.length > 0) {
                    const pwnedIds = pwnedPasswords.map(p => p.id);

                    const groupsList = document.getElementById('groups-list');
                    if (groupsList.querySelector('[data-group-id="pwned"]') === null) {
                        const pwnedFilterItem = document.createElement('li');
                        pwnedFilterItem.innerHTML = `<a href="#" data-group-id="pwned" class="group-filter flex items-center gap-2 px-2 py-1.5 rounded-md text-red-400 font-bold"><i class="ph-fill ph-warning"></i> Vulneráveis</a>`;
                        groupsList.prepend(pwnedFilterItem);
                        addFilterListeners();
                    }
                    
                    passwordCards.forEach(card => {
                        const cardId = parseInt(card.dataset.id, 10);
                        if (pwnedIds.includes(cardId)) {
                            card.classList.add('border-red-500', 'border-2');
                            card.dataset.isPwned = 'true';
                            const titleElement = card.querySelector('h4');
                            if (titleElement && !titleElement.querySelector('.pwned-badge')) {
                                titleElement.insertAdjacentHTML('beforeend', '<span class="pwned-badge text-xs bg-red-600 text-white font-bold px-2 py-0.5 rounded-full ml-2">VAZOU</span>');
                            }
                        }
                    });
                }
            } catch(e) { console.error("Erro ao processar dados de senhas vazadas:", e); }
            sessionStorage.removeItem('pwnedPasswordsResult');
        }

        const highlightId = <?= json_encode($finalHighlightId) ?>;
        if (highlightId) {
            const cardToHighlight = document.querySelector(`.password-card[data-id='${highlightId}']`);
            if (cardToHighlight) {
                cardToHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
                cardToHighlight.style.transition = 'all 0.5s';
                cardToHighlight.style.backgroundColor = '#1d4ed8';
                setTimeout(() => { cardToHighlight.style.backgroundColor = ''; }, 2000);
            }
        }
    });

    function addFilterListeners() {
        document.querySelectorAll('.group-filter').forEach(filter => {
            filter.addEventListener('click', handleFilterClick, { once: true });
        });
    }

    function handleFilterClick(e) {
        e.preventDefault();
        document.querySelectorAll('.group-filter').forEach(f => f.classList.remove('active-group'));
        this.classList.add('active-group');
        activeGroupId = this.dataset.groupId;
        filterPasswords();
        addFilterListeners();
    }

    function filterPasswords() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;
        passwordCards.forEach(card => {
            const cardGroupId = card.dataset.groupId;
            const cardSearchTerm = card.dataset.searchTerm;
            const isPwned = card.dataset.isPwned === 'true';
            let groupMatch = false;

            if (activeGroupId === 'all') { groupMatch = true; }
            else if (activeGroupId === 'pwned') { groupMatch = isPwned; }
            else { groupMatch = (activeGroupId === cardGroupId); }

            const searchMatch = cardSearchTerm.includes(searchTerm);

            if (groupMatch && searchMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    function toggleDetails(element) {
        const details = element.nextElementSibling;
        details.classList.toggle('hidden');
    }

    function copyToClipboard(text, button) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="ph ph-check-circle text-green-400"></i>';
            setTimeout(() => { button.innerHTML = originalIcon; }, 1500);
        });
    }

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

    async function openEditModal(id) {
        const contentDiv = document.getElementById('edit-modal-content');
        contentDiv.innerHTML = `<div class="p-8 text-center text-gray-300">Carregando...</div>`;
        openModal('edit-modal');

        const formData = new FormData();
        formData.append('id', id);
        const response = await fetch(`processa_senha.php?action=get_details`, { method: 'POST', body: formData });
        const data = await response.json();

        if (!data.success) {
            contentDiv.innerHTML = `<div class="p-6 text-center text-red-400">${data.message || 'Ocorreu um erro.'}</div>`;
            return;
        }

        const allGroups = <?= json_encode($groups) ?>;
        let groupsOptions = '<option value="">Nenhum</option>';
        allGroups.forEach(group => {
            const isSelected = group.id == data.group_id ? 'selected' : '';
            groupsOptions += `<option value="${group.id}" ${isSelected}>${group.name}</option>`;
        });

        let recoveryCodesText = '';
        if (data.recovery_codes) {
            try {
                const codes = JSON.parse(data.recovery_codes);
                recoveryCodesText = codes.map(c => c.code).join('\n');
            } catch(e) { recoveryCodesText = data.recovery_codes; }
        }
        const hasRecoveryCodes = recoveryCodesText.length > 0;

        contentDiv.innerHTML = `
            <form action="processa_senha.php" method="POST" class="p-6 space-y-4">
                <h2 class="text-xl font-semibold text-white">Editar Senha</h2>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="${data.id}">
                <input type="text" name="name" id="name-edit" value="${data.name}" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-400">Grupo Existente</label>
                        <select name="group_id" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">${groupsOptions}</select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-400">Ou Novo Grupo</label>
                        <input type="text" name="new_group_name" placeholder="Criar ou mover para novo grupo" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                    </div>
                </div>
                <input type="url" name="site_url" value="${data.site_url || ''}" placeholder="Site" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <input type="text" name="email" value="${data.email || ''}" placeholder="Email / Usuário" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <div class="relative">
                    <input type="text" name="password_text" id="password_text-edit" value="${data.password}" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white pr-20">
                    <button type="button" onclick="generatePassword('edit')" class="absolute inset-y-0 right-0 px-3 text-sm font-semibold text-blue-400 hover:text-blue-300">Gerar</button>
                </div>
                <textarea name="description" placeholder="Descrição..." rows="2" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">${data.description || ''}</textarea>
                <div class="flex items-center">
                    <input id="recovery-toggle-edit" onchange="toggleRecovery('edit')" type="checkbox" ${hasRecoveryCodes ? 'checked' : ''} class="h-4 w-4 text-blue-500 bg-gray-700 border-gray-600 rounded">
                    <label for="recovery-toggle-edit" class="ml-2 block text-sm text-gray-300">Códigos de recuperação</label>
                </div>
                <div id="recovery-container-edit" class="${hasRecoveryCodes ? '' : 'hidden'}">
                    <textarea name="recovery_codes" placeholder="Cole os códigos aqui..." rows="4" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">${recoveryCodesText}</textarea>
                </div>
                <div class="flex justify-end gap-4 pt-2">
                    <button type="button" onclick="closeModal('edit-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar Alterações</button>
                </div>
            </form>`;
    }

    function toggleRecovery(prefix) {
        document.getElementById(`recovery-container-${prefix}`).classList.toggle('hidden');
    }

    function generatePassword(prefix) {
        const passwordInput = document.getElementById(`password_text-${prefix}`);
        const length = 16;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        let password = "";
        for (let i = 0; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        passwordInput.value = password;
    }
</script>

<?php render_footer(); ?>