<?php
$pageTitle = 'Autenticadores 2FA';
require_once __DIR__ . '/navbar.php';
render_header($pageTitle);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dao/AuthenticatorDAO.php';
require_once __DIR__ . '/../dao/PasswordDAO.php';
require_once __DIR__ . '/../security/Crypto.php';

use OTPHP\TOTP;

$authDao = new AuthenticatorDAO();
$passDao = new PasswordDAO();
$authenticators = [];
$passwords = [];
$masterPassword = $_SESSION['master_password_session'] ?? null;
$error_message = $_SESSION['2fa_error'] ?? null;
unset($_SESSION['2fa_error']);

if ($masterPassword) {
    $authenticators = $authDao->getAllByUserId($_SESSION['user_id']);
    $passwords = $passDao->getPasswordsWithout2FA($_SESSION['user_id']);
}
?>
<header class="mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div><h1 class="text-2xl sm:text-3xl font-bold text-white">Autenticadores 2FA</h1><p class="text-gray-400 text-sm sm:text-base">Seus códigos de verificação (TOTP).</p></div>
    <div class="flex items-center gap-2 sm:gap-4">
        <button onclick="openModal('add-modal')" class="px-3 py-2 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2 text-xs sm:text-sm"><i class="ph-plus-circle"></i> Adicionar</button>
        <a href="dashboard.php" class="px-3 py-2 text-xs sm:text-sm bg-gray-700 hover:bg-gray-600 rounded-md">Voltar</a>
    </div>
</header>

<div class="bg-gray-800 border border-gray-700 p-4 sm:p-6 rounded-lg shadow-md">
    <?php if ($error_message): ?>
        <div class="p-4 mb-4 bg-red-900/50 border border-red-700 text-red-300 rounded-md">
            <h3 class="font-bold">Erro ao Adicionar</h3>
            <p><?= htmlspecialchars($error_message) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!$masterPassword): ?>
        <div class="text-center p-4 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-md">
            <h3 class="font-bold">Cofre Bloqueado</h3>
            <p>Desbloqueie o cofre na <a href="senhas.php" class="text-blue-400 font-semibold hover:underline">página de senhas</a> para ver os autenticadores.</p>
        </div>
    <?php elseif (empty($authenticators)): ?>
        <p class="text-center text-gray-400">Nenhum autenticador adicionado ainda.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($authenticators as $auth): ?>
                <div class="p-4 border border-gray-700 rounded-md bg-gray-900 text-center">
                    <h3 class="font-semibold text-white mb-2 truncate" title="<?= htmlspecialchars(Crypto::decrypt($auth['password_name'], $masterPassword)) ?>">
                        <?= htmlspecialchars(Crypto::decrypt($auth['password_name'], $masterPassword)) ?>
                    </h3>
                    <?php
                        $code = 'ERRO';
                        try {
                            $secret = Crypto::decrypt($auth['secret_key'], $masterPassword);
                            $totp = TOTP::create($secret);
                            $code = $totp->now();
                        } catch (Exception $e) { $code = 'Chave Inválida'; }
                    ?>
                    <p class="text-3xl font-mono tracking-widest <?= $code === 'Chave Inválida' ? 'text-red-500 text-base' : 'text-teal-400' ?>" id="code-<?= $auth['id'] ?>">
                        <?= $code !== 'Chave Inválida' ? chunk_split($code, 3, ' ') : $code ?>
                    </p>
                    <div class="w-full bg-gray-700 rounded-full h-1 mt-2">
                        <div class="bg-teal-500 h-1 rounded-full" id="progress-<?= $auth['id'] ?>"></div>
                    </div>
                    <a href="processa_autenticador.php?action=delete&id=<?= $auth['id'] ?>" onclick="return confirm('Tem certeza?')" class="text-red-500 hover:text-red-400 text-xs mt-2 inline-block">Apagar</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="add-modal" class="modal hidden fixed inset-0 bg-black bg-opacity-70 items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md">
        <form action="processa_autenticador.php" method="POST" class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-white">Novo Autenticador</h2>
            <input type="hidden" name="action" value="create">
            <div>
                <label class="text-sm font-medium text-gray-400">Associar ao Login:</label>
                <select name="password_id" required class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                    <option value="">Selecione um login...</option>
                    <?php foreach($passwords as $pass): ?>
                        <option value="<?= $pass['id'] ?>"><?= htmlspecialchars(Crypto::decrypt($pass['name'], $masterPassword)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-400">Chave Secreta (TOTP Key)</label>
                <input type="text" name="secret_key" placeholder="Cole a chave secreta aqui" required class="w-full mt-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
            </div>
            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('add-modal')" class="px-4 py-2 bg-gray-600 rounded hover:bg-gray-500">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar</button>
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
    
    function updateCodes() {
        const period = 30;
        const seconds = new Date().getSeconds() % period;
        const progress = (seconds / period) * 100;

        document.querySelectorAll('[id^=progress-]').forEach(progressBar => {
            progressBar.style.width = `${100 - progress}%`;
        });

        if (seconds === 0) {
            setTimeout(() => window.location.reload(), 1500);
        }
    }
    setInterval(updateCodes, 1000);
    updateCodes();
</script>
<?php render_footer(); ?>