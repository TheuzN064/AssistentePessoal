<?php
// /public/ips.php
require_once __DIR__ . '/../init.php';

$pageTitle = 'IPs de Acesso';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

$logFile = "/var/log/nginx/access.log";
$ips = [];
$error = null;

if (!is_readable($logFile)) {
    $error = "Erro: O arquivo de log não foi encontrado ou o servidor não tem permissão para lê-lo em: $logFile";
} else {
    // Lógica otimizada para ler o arquivo linha por linha, sem esgotar a memória
    $handle = fopen($logFile, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // Só pegar requisições GET para a página inicial
            if (preg_match('/^(\S+) .*?"GET \/($|index\.php)/', $line, $matches)) {
                $ip = $matches[1] ?? null;
                if ($ip) {
                    if (!isset($ips[$ip])) $ips[$ip] = 0;
                    $ips[$ip]++;
                }
            }
        }
        fclose($handle);
    } else {
        $error = "Erro: Não foi possível abrir o arquivo de log para leitura.";
    }
}

// Ordena do IP mais frequente para o menos frequente
arsort($ips);
?>

<header class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-white">Análise de Acesso à Página Inicial</h1>
        <p class="text-gray-400 text-sm sm:text-base">Contagem de acessos à página inicial por endereço de IP.</p>
    </div>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <?php if ($error): ?>
        <div class="p-4 bg-red-900/50 border border-red-700 text-red-300 rounded-md">
            <h3 class="font-bold">Falha ao Ler o Log</h3>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php elseif (empty($ips)): ?>
        <p class="text-center text-gray-400 py-8">Nenhum acesso à página inicial encontrado no arquivo de log.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Endereço de IP
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Quantidade de Acessos
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    <?php foreach ($ips as $ip => $count): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-200">
                                <?= htmlspecialchars($ip) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                                <?= $count ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php render_footer(); ?>