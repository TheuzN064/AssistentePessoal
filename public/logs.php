<?php
$pageTitle = 'Logs do Sistema';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle); // <-- ADICIONADO PARA CORRIGIR A RENDERIZAÇÃO

$logFile = __DIR__ . '/../app_log.txt';
$logs = [];
if (file_exists($logFile)) {
    $rawLogs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logs = array_reverse($rawLogs);
}

function getLogLevelClass($level) {
    switch ($level) {
        case 'ERROR': return 'bg-red-500/20 text-red-400';
        case 'PANIC': return 'bg-red-800/40 text-red-300 font-bold';
        case 'WARNING': return 'bg-yellow-500/20 text-yellow-400';
        case 'INFO': default: return 'bg-blue-500/20 text-blue-400';
    }
}
?>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Logs do Sistema</h1>
            <p class="text-gray-400 text-sm">Atividades, erros e eventos de segurança registrados.</p>
        </div>
        <?php if (!empty($logs)): ?>
            <a href="processa_logs.php?action=clear" onclick="return confirm('Tem certeza que deseja apagar todos os logs? Esta ação não pode ser desfeita.');" class="w-full sm:w-auto px-4 py-2 text-center font-semibold bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center justify-center gap-2">
                <i class="ph-fill ph-trash"></i>
                <span>Limpar Logs</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($logs)): ?>
        <div class="text-center py-10">
            <i class="ph-fill ph-file text-5xl text-gray-600"></i>
            <p class="mt-4 text-gray-500">Nenhum log registrado ainda.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-300">
                <thead class="bg-gray-700/50 text-xs uppercase">
                    <tr>
                        <th scope="col" class="px-4 py-3">Nível</th>
                        <th scope="col" class="px-4 py-3">Data e Hora</th>
                        <th scope="col" class="px-4 py-3">Endereço IP</th>
                        <th scope="col" class="px-4 py-3">Mensagem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $logEntry): 
                        preg_match('/\[(.*?)\] \[(.*?)\] \[(.*?)\] (.*)/', $logEntry, $matches);
                        if (count($matches) === 5) {
                            [$full, $timestamp, $ip, $level, $message] = $matches;
                        } else {
                            $timestamp = date('Y-m-d H:i:s'); $level = 'INFO'; $ip = '-'; $message = $logEntry;
                        }
                    ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= getLogLevelClass($level) ?>">
                                <?= htmlspecialchars($level) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($timestamp) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap font-mono"><?= htmlspecialchars($ip) ?></td>
                        <td class="px-4 py-3"><?= htmlspecialchars($message) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php 
render_footer(); // <-- ADICIONADO PARA FECHAR O LAYOUT CORRETAMENTE
?>