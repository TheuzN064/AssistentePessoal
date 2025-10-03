<?php
// Garante o carregamento de todas as classes necessárias no topo do arquivo.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dao/DashboardDAO.php';

$pageTitle = 'Dashboard';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

$dao = new DashboardDAO();
$summaryData = $dao->getSummaryData($_SESSION['user_id']);

$labels = json_encode(array_keys($summaryData));
$data = json_encode(array_values($summaryData));
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-gray-800 border border-gray-700 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-white mb-4">Resumo do Cofre</h2>
        <div class="h-80 relative">
            <canvas id="summaryChart"></canvas>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-gray-800 border border-gray-700 p-6 rounded-lg shadow-lg flex flex-col justify-center items-center text-center">
            <i class="ph-fill ph-key text-5xl text-yellow-400 mb-4"></i>
            <h2 class="text-xl font-bold text-white mb-2">Cofre de Senhas</h2>
            <?php if (!isset($_SESSION['master_password_session'])): ?>
                <p class="text-gray-400 mb-4 text-sm">Digite sua senha mestra para acessar os dados sensíveis.</p>
                <form action="processa_senha.php" method="POST" class="w-full">
                    <input type="hidden" name="action" value="unlock_vault">
                    <input type="hidden" name="redirect_to" value="dashboard.php">
                    <input type="password" name="master_password_for_session" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white mb-3" placeholder="Senha Mestra" required>
                    <button type="submit" class="w-full px-4 py-2 font-semibold bg-blue-600 text-white rounded-md hover:bg-blue-700">Desbloquear</button>
                    <?php if (isset($_GET['error']) && $_GET['error'] === 'unlock_failed'): ?>
                        <p class="text-red-400 text-xs mt-2">Senha mestra incorreta.</p>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <p class="text-green-400 font-semibold mb-4">Cofre Desbloqueado</p>
                <p class="text-gray-400 text-sm">A sessão do cofre será bloqueada após um período de inatividade.</p>
            <?php endif; ?>
        </div>
        
        <div id="pwned-check-widget" class="bg-gray-800 border border-gray-700 p-6 rounded-lg shadow-lg text-center">
            <i class="ph-fill ph-shield-warning text-5xl text-orange-400 mb-4"></i>
            <h2 class="text-xl font-bold text-white mb-2">Segurança das Senhas</h2>
            <div id="pwned-check-result">
                <?php if (isset($_SESSION['master_password_session'])): ?>
                    <p class="text-gray-400 mb-4 text-sm">Verifique se alguma de suas senhas já apareceu em vazamentos de dados.</p>
                    <button id="pwned-check-button" onclick="checkPwnedPasswords()" class="w-full px-4 py-2 font-semibold bg-orange-600 text-white rounded-md hover:bg-orange-700">Verificar Agora</button>
                <?php else: ?>
                    <p class="text-gray-400 text-sm">Desbloqueie o cofre para poder verificar a segurança das suas senhas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('summaryChart');
        if (ctx) {
            const labels = <?= $labels ?>;
            const data = <?= $data ?>;
            
            const filteredLabels = [];
            const filteredData = [];
            let totalItems = 0;
            data.forEach((value, index) => {
                totalItems += value;
                if (value > 0) {
                    filteredLabels.push(labels[index]);
                    filteredData.push(value);
                }
            });

            if (totalItems > 0) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: filteredLabels,
                        datasets: [{
                            label: 'Itens no Cofre',
                            data: filteredData,
                            backgroundColor: ['#3B82F6', '#10B981', '#F97316', '#8B5CF6', '#EC4899', '#6366f1'],
                            borderColor: '#1f2937',
                            borderWidth: 4,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9CA3AF',
                                    font: { size: 14 }
                                }
                            }
                        }
                    }
                });
            } else {
                 ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500"><p>Nenhum item no cofre para exibir.</p></div>';
            }
        }
    });

    async function checkPwnedPasswords() {
        const resultDiv = document.getElementById('pwned-check-result');
        resultDiv.innerHTML = `
            <div class="flex items-center justify-center gap-2 text-gray-400">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Verificando... Isso pode levar um momento.</span>
            </div>`;

        try {
            const response = await fetch('verifica_vazamentos.php');
            const data = await response.json();

            if (data.success) {
                const pwnedCount = data.pwned.length;
                sessionStorage.setItem('pwnedPasswordsResult', JSON.stringify(data.pwned));

                if (pwnedCount > 0) {
                    resultDiv.innerHTML = `
                        <p class="font-semibold text-red-400 text-lg">${pwnedCount} senha(s) vulnerável(is)!</p>
                        <p class="text-gray-400 mb-4 text-sm">Encontramos senhas suas em vazamentos de dados conhecidos.</p>
                        <a href="senhas.php" class="w-full block px-4 py-2 font-semibold bg-red-600 text-white rounded-md hover:bg-red-700">Ver Senhas Afetadas</a>`;
                } else {
                    resultDiv.innerHTML = `
                        <p class="font-semibold text-green-400 text-lg">Nenhuma senha vazada!</p>
                        <p class="text-gray-400 text-sm">Suas senhas estão seguras e não foram encontradas em vazamentos conhecidos.</p>`;
                }
            } else {
                resultDiv.innerHTML = `<p class="text-red-400 text-sm">Erro: ${data.message || 'Não foi possível completar a verificação.'}</p>`;
            }
        } catch (error) {
            resultDiv.innerHTML = `<p class="text-red-400 text-sm">Ocorreu um erro de comunicação ao verificar as senhas.</p>`;
        }
    }
</script>

<?php
render_footer();
?>