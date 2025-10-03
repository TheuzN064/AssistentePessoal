<?php
// Garante que o autoloader do Composer seja o primeiro a ser carregado.
require_once __DIR__ . '/../vendor/autoload.php';

// ADICIONADO: Inclusão manual dos DAOs e Classes para corrigir qualquer erro "Class not found".
require_once __DIR__ . '/../dao/SubscriptionDAO.php';
require_once __DIR__ . '/../dao/PasswordDAO.php';
require_once __DIR__ . '/../dao/PersonDAO.php';
require_once __DIR__ . '/../dao/CardDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

$pageTitle = 'Gerenciar Assinaturas';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);


// O restante do script continua como planejado.
$subDao = new SubscriptionDAO();
$passDao = new PasswordDAO();
$personDao = new PersonDAO();
$cardDao = new CardDAO();
$subscriptions = [];
$passwords = [];
$people = [];
$cards = [];
$masterPassword = $_SESSION['master_password_session'] ?? null;

if ($masterPassword) {
    $rawSubscriptions = $subDao->getAllByUserId($_SESSION['user_id']);
    $passwords = $passDao->getByUserId($_SESSION['user_id']);
    $people = $personDao->getAllByUserId($_SESSION['user_id']);
    $cards = $cardDao->getAllByUserId($_SESSION['user_id']);

    foreach($rawSubscriptions as $sub) {
        try {
            $renewalDay = $sub['renewal_day'];
            if (empty($renewalDay) || !is_numeric($renewalDay)) {
                continue; // Pula assinaturas sem um dia de renovação válido
            }
            $currentDay = date('j');
            $currentMonth = date('n');
            $currentYear = date('Y');
            $lastDayOfCurrentMonth = date('t', strtotime("$currentYear-$currentMonth-01"));
            $dayToUse = min($renewalDay, $lastDayOfCurrentMonth);
            $renewalDateThisMonth = new DateTime("$currentYear-$currentMonth-$dayToUse");
            
            $today = new DateTime();
            $today->setTime(0, 0, 0);

            $nextRenewalDate = $renewalDateThisMonth;
            if ($today > $nextRenewalDate) {
                $nextMonthDate = (new DateTime('now'))->modify('+1 month');
                $nextMonth = $nextMonthDate->format('n');
                $nextYear = $nextMonthDate->format('Y');
                $lastDayOfNextMonth = $nextMonthDate->format('t');
                $dayForNextMonth = min($renewalDay, $lastDayOfNextMonth);
                $nextRenewalDate = new DateTime("$nextYear-$nextMonth-$dayForNextMonth");
            }

            $diff = $today->diff($nextRenewalDate);
            $daysUntilRenewal = (int)$diff->format('%r%a');

            $status = 'ok';
            if ($daysUntilRenewal < 0) $status = 'overdue';
            elseif ($daysUntilRenewal == 0) $status = 'overdue';
            elseif ($daysUntilRenewal <= 7) $status = 'due_soon';
            
            $sub['next_renewal_formatted'] = $nextRenewalDate->format('d/m/Y');
            $sub['days_until_renewal'] = $daysUntilRenewal;
            $sub['status'] = $status;
            
            $subscriptions[] = $sub;

        } catch (Throwable $e) {
            continue;
        }
    }
}
?>
<header class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Gerenciar Assinaturas</h1><p class="text-gray-400 text-sm sm:text-base">Controle seus serviços recorrentes.</p></div>
    <div class="flex items-center gap-2 sm:gap-4">
        <button onclick="openModal('add-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md text-xs sm:text-sm">Adicionar</button>
    </div>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <?php if (!$masterPassword): ?>
        <div class="text-center p-4 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-md">
            <h3 class="font-bold">Cofre Bloqueado</h3>
            <p>Desbloqueie o cofre na <a href="senhas.php" class="text-blue-400 font-semibold hover:underline">página de senhas</a> para gerenciar assinaturas.</p>
        </div>
    <?php else: ?>
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
            <div class="relative flex-grow">
                <input type="text" id="search-input" placeholder="Buscar assinatura..." class="w-full bg-gray-900 border border-gray-700 rounded-lg py-2 pl-10 pr-4 text-white">
                <i class="ph ph-magnifying-glass absolute top-1/2 left-3 -translate-y-1/2 text-gray-500"></i>
            </div>
            <div id="filter-buttons" class="flex items-center gap-2 flex-wrap">
                <button data-filter="all" class="filter-btn active-filter">Todos</button>
                <button data-filter="due_soon" class="filter-btn">Vencendo</button>
                <button data-filter="shared" class="filter-btn">Compartilhados</button>
            </div>
        </div>
        
        <div id="subscriptions-list" class="space-y-3">
            <?php if (empty($subscriptions)): ?>
                <p class="text-center text-gray-400 py-8">Nenhuma assinatura adicionada ainda.</p>
            <?php else: ?>
                <?php foreach ($subscriptions as $sub):
                    $decryptedName = htmlspecialchars(Crypto::decrypt($sub['name'], $masterPassword));
                    $decryptedValue = htmlspecialchars(Crypto::decrypt($sub['value'], $masterPassword));
                    
                    $statusColor = 'bg-green-500';
                    $statusTitle = 'Em dia';
                    if ($sub['status'] === 'overdue') { $statusColor = 'bg-red-500'; $statusTitle = 'Vencido ou vence hoje'; }
                    elseif ($sub['status'] === 'due_soon') { $statusColor = 'bg-yellow-500'; $statusTitle = 'Vencendo em breve'; }
                ?>
                <div class="subscription-card bg-gray-900 border border-gray-700 rounded-lg"
                     data-name="<?= strtolower($decryptedName) ?>"
                     data-status="<?= $sub['status'] ?>"
                     data-shared="<?= $sub['is_shared'] ?>">
                    <div class="p-4 flex items-center gap-4 cursor-pointer" onclick="toggleDetails(this)">
                        <div class="w-4 h-4 rounded-full flex-shrink-0 <?= $statusColor ?>" title="<?= $statusTitle ?>"></div>
                        <div class="flex-grow overflow-hidden">
                            <h4 class="font-bold text-white truncate"><?= $decryptedName ?></h4>
                            <p class="text-sm text-gray-400">R$ <?= $decryptedValue ?> / mês</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-semibold text-white">Vence em <?= $sub['next_renewal_formatted'] ?></p>
                            <p class="text-xs text-gray-500">
                                <?php 
                                    if ($sub['days_until_renewal'] < 0) echo abs($sub['days_until_renewal']) . " dia(s) atrasado";
                                    elseif ($sub['days_until_renewal'] == 0) echo "Vence hoje";
                                    elseif ($sub['days_until_renewal'] == 1) echo "Vence amanhã";
                                    else echo "Faltam {$sub['days_until_renewal']} dias";
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="details-content hidden border-t border-gray-700 p-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <p><strong>Cartão Usado:</strong> <?= $sub['card_name'] ? htmlspecialchars(Crypto::decrypt($sub['card_name'], $masterPassword)) : 'N/A' ?></p>
                            <p><strong>Login Associado:</strong> 
                                <?php if ($sub['password_id'] && $sub['password_name']): ?>
                                    <a href="senhas.php?highlight_id=<?= $sub['password_id'] ?>" class="text-blue-400 hover:underline"><?= htmlspecialchars(Crypto::decrypt($sub['password_name'], $masterPassword)) ?></a>
                                <?php else: ?> N/A <?php endif; ?>
                            </p>
                        </div>
                        <?php if ($sub['is_shared'] && !empty($sub['shared_with_details'])): ?>
                            <form action="processa_assinatura.php" method="POST">
                                <input type="hidden" name="action" value="update_payments">
                                <input type="hidden" name="subscription_id" value="<?= $sub['id'] ?>">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-semibold text-white text-sm">Pagamentos do Mês (<?= date('F') ?>):</h4>
                                    <div>
                                        <a href="historico.php?id=<?= $sub['id'] ?>" class="px-2 py-1 text-xs bg-gray-700 rounded-md hover:bg-gray-600 mr-2">Histórico</a>
                                        <?php
                                            $subNameJs = addslashes($decryptedName);
                                            $totalValueJs = addslashes($decryptedValue);
                                            $peopleForJs = array_map(function($p) use ($masterPassword) { return ['name' => htmlspecialchars(Crypto::decrypt($p['name'], $masterPassword)), 'has_paid' => $p['has_paid']]; }, $sub['shared_with_details']);
                                            $peopleJson = htmlspecialchars(json_encode($peopleForJs), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <button type="button" onclick="copyPaymentList(this, '<?= $subNameJs ?>', '<?= $peopleJson ?>', '<?= $totalValueJs ?>')" class="px-2 py-1 text-xs bg-gray-700 rounded-md hover:bg-gray-600">Copiar Lista</button>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <?php foreach($sub['shared_with_details'] as $person): ?>
                                    <div class="flex items-center p-2 rounded-md bg-gray-800">
                                        <input type="checkbox" name="paid_people_ids[]" value="<?= $person['id'] ?>" id="payment_<?= $sub['id'] ?>_<?= $person['id'] ?>" <?= $person['has_paid'] ? 'checked' : '' ?> class="h-4 w-4 text-green-500 bg-gray-700 border-gray-600 rounded">
                                        <label for="payment_<?= $sub['id'] ?>_<?= $person['id'] ?>" class="ml-3 text-gray-300 text-sm"><?= htmlspecialchars(Crypto::decrypt($person['name'], $masterPassword)) ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">Salvar Pagamentos</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div id="no-results" class="hidden text-center text-gray-500 py-8">
                <p>Nenhuma assinatura encontrada.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="add-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4 z-50">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md">
        <form action="processa_assinatura.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Nova Assinatura</h2>
            <input type="hidden" name="action" value="create">
            <input type="text" name="name" placeholder="Nome (Ex: Netflix)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            <div class="grid grid-cols-2 gap-4">
                <input type="number" step="0.01" name="value" placeholder="Valor (Ex: 39.90)" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                <input type="number" name="renewal_day" min="1" max="31" placeholder="Dia da Cobrança" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-400">Cartão de Pagamento</label>
                <select name="card_id" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                    <option value="">Nenhum</option>
                    <?php foreach($cards as $card): ?>
                        <option value="<?= $card['id'] ?>"><?= htmlspecialchars(Crypto::decrypt($card['card_name'], $masterPassword)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-400">Login Associado</label>
                <select name="password_id" class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                    <option value="">Nenhum</option>
                    <?php foreach($passwords as $pass): ?>
                        <option value="<?= $pass['id'] ?>"><?= htmlspecialchars(Crypto::decrypt($pass['name'], $masterPassword)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center">
                <input id="is_shared" name="is_shared" type="checkbox" onchange="toggleShared()" class="h-4 w-4 text-blue-600 rounded bg-gray-700 border-gray-600">
                <label for="is_shared" class="ml-2 text-sm text-gray-300">Assinatura Compartilhada</label>
            </div>
            <div id="people-container" class="hidden">
                <label class="text-sm font-medium text-gray-400">Compartilhada com:</label>
                <div class="mt-2 space-y-2 max-h-32 overflow-y-auto border border-gray-600 p-2 rounded-md">
                    <?php foreach($people as $person): ?>
                    <div class="flex items-center">
                        <input type="checkbox" name="people_ids[]" value="<?= $person['id'] ?>" id="person_<?= $person['id'] ?>" class="h-4 w-4 text-blue-600 rounded bg-gray-700 border-gray-600">
                        <label for="person_<?= $person['id'] ?>" class="ml-2 text-sm text-gray-300"><?= htmlspecialchars(Crypto::decrypt($person['name'], $masterPassword)) ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('add-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<style>
    .filter-btn { padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 600; font-size: 0.875rem; background-color: #374151; color: #d1d5db; transition: all 0.2s; }
    .filter-btn.active-filter { background-color: #2563eb; color: white; }
    .filter-btn:hover:not(.active-filter) { background-color: #4b5563; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('subscriptions-list')) return;
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('search-input');
    const subscriptionCards = document.querySelectorAll('.subscription-card');
    const noResultsMessage = document.getElementById('no-results');
    let activeFilter = 'all';
    function filterSubscriptions() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;
        subscriptionCards.forEach(card => {
            const status = card.dataset.status;
            const isShared = card.dataset.shared === '1';
            const name = card.dataset.name;
            let filterMatch = false;
            if (activeFilter === 'all') { filterMatch = true; } 
            else if (activeFilter === 'due_soon') { filterMatch = (status === 'due_soon' || status === 'overdue'); } 
            else if (activeFilter === 'shared') { filterMatch = isShared; }
            const searchMatch = name.includes(searchTerm);
            if (filterMatch && searchMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active-filter'));
            this.classList.add('active-filter');
            activeFilter = this.dataset.filter;
            filterSubscriptions();
        });
    });
    searchInput.addEventListener('input', filterSubscriptions);
});
function toggleDetails(element) {
    const details = element.nextElementSibling;
    details.classList.toggle('hidden');
}
function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
function toggleShared() { document.getElementById('people-container').classList.toggle('hidden', !document.getElementById('is_shared').checked); }
function copyPaymentList(button, subName, peopleJson, totalValue) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = peopleJson;
    const decodedJson = tempDiv.textContent || tempDiv.innerText || "";
    const people = JSON.parse(decodedJson);
    if (people.length === 0) return; // evita divisão por zero

    const totalPeople = people.length; // apenas as pessoas na lista
    const valuePerPerson = (parseFloat(totalValue.replace(',', '.')) / totalPeople)
        .toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    let copyText = `Controle de Pagamento - ${subName}:\n`;
    people.forEach(person => {
        const status = person.has_paid ? '✅ ' : '❌ ';
        copyText += `- ${person.name}: ${status}\n`;
    });
    copyText += `\nValor para cada um: ${valuePerPerson}\n`;
    copyText += `\nPIX para pagamento: matheuspagamentos@icloud.com`;

    copyToClipboard(copyText, button);
}

function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const original = button.innerHTML;
        button.innerHTML = 'Copiado!';
        setTimeout(() => { button.innerHTML = original; }, 2000);
    });
}
</script>

<?php render_footer(); ?>