<?php
session_start();
require_once __DIR__ . '/../security/SecurityManager.php';

if (SecurityManager::isPanicModeActive()) {
    $_GET['error'] = 'panic';
} elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistente Pessoal - Login</title>
    
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Assis">
    <meta name="theme-color" content="#1F2937"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Na tela de login, mantemos o bloqueio total pois não há rolagem */
        html, body {
            overscroll-behavior: none;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 bg-gradient-to-br from-gray-900 to-slate-800">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="w-full max-w-sm p-8 space-y-6 bg-gray-800/50 border border-gray-700 rounded-2xl shadow-2xl backdrop-blur-sm">
            
            <div class="text-center">
                <i class="ph-fill ph-shield-check text-5xl text-blue-400"></i>
                <h2 class="mt-4 text-3xl font-bold text-white">Assistente Pessoal</h2>
                <p class="text-gray-400">Acesso seguro ao seu cofre</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'panic'): ?>
                <div class="p-4 bg-red-900/50 border border-red-700 text-red-300 rounded-md text-sm">
                    <h3 class="font-bold">Sistema Bloqueado</h3>
                    <p>O sistema foi desativado por segurança. Contate o administrador.</p>
                </div>
            <?php else: ?>
            <form action="processa_login.php" method="POST" class="space-y-6">
                <div class="relative">
                    <i class="ph ph-user absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                    <input id="username" name="username" type="text" required class="w-full px-3 py-2 pl-10 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" placeholder="Usuário" autocomplete="username">
                </div>
                
                <div class="relative">
                    <i class="ph ph-key absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"></i>
                    <input id="master-password" name="master-password" type="password" required class="w-full px-3 py-2 pl-10 pr-10 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-white" placeholder="Senha Mestra" autocomplete="current-password">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 hover:text-white" title="Mostrar/Ocultar senha">
                        <i id="eye-icon" class="ph ph-eye"></i>
                    </button>
                </div>

                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <p class="text-red-400 text-sm text-center">Usuário ou senha inválidos.</p>
                <?php endif; ?>

                <button type="submit" class="w-full px-4 py-2.5 font-semibold bg-blue-600 text-white hover:bg-blue-700 rounded-md transition-colors duration-300">Entrar</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('master-password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('ph-eye');
                eyeIcon.classList.add('ph-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('ph-eye-slash');
                eyeIcon.classList.add('ph-eye');
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('service-worker.js').then(registration => {
                    console.log('ServiceWorker registrado com sucesso: ', registration.scope);
                }, err => {
                    console.log('Registro do ServiceWorker falhou: ', err);
                });
            });
        }
    </script>
</body>
</html>