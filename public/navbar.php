<?php
// /public/navbar.php

function render_header($pageTitle) {
    require_once __DIR__ . '/../security/SessionManager.php';
    SessionManager::checkActivity();

    function get_nav_item($page_name, $icon_name, $text) {
        $isActive = (basename($_SERVER['PHP_SELF']) == $page_name);
        $class = $isActive
            ? 'bg-gray-700 text-white'
            : 'text-gray-400 hover:bg-gray-700 hover:text-white';
        
        echo "<a href='{$page_name}' class='flex items-center gap-3 px-4 py-2.5 rounded-md {$class} transition-colors duration-200 justify-start md:justify-start'>
                <i class='ph-fill ph-{$icon_name} text-xl'></i>
                <span class='font-medium sidebar-text'>{$text}</span>
              </a>";
    }
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Assistente Pessoal</title>
    
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Assis">
    <meta name="theme-color" content="#1F2937"/>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        html, body {
            overflow: hidden;
            height: 100%;
        }
        main {
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        #sidebar nav {
            overflow-y: auto;
        }
        body { 
            background-color: #111827; 
            color: #d1d5db;
            overscroll-behavior-y: contain;
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
        #sidebar, #main-content { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-900 font-sans">

<div class="flex h-screen">
    <div id="sidebar-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/60 z-30 hidden md:hidden"></div>

    <aside id="sidebar" class="bg-gray-800 text-gray-300 w-64 flex-shrink-0 fixed top-0 left-0 h-full flex flex-col p-4 z-40 
                               -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0">
        
        <div onclick="toggleDesktopSidebar()" class="flex items-center gap-3 mb-8 px-2 cursor-pointer hover:bg-gray-700/50 rounded-lg p-2 -mx-2 flex-shrink-0" title="Recolher/Expandir Menu">
            <i class="ph-fill ph-shield-check text-3xl text-blue-400"></i>
            <span class="font-bold text-xl text-white sidebar-text whitespace-nowrap">assis_pessoal</span>
        </div>

        <nav class="flex flex-col gap-y-1 flex-grow">
            <?php get_nav_item('dashboard.php', 'house', 'Dashboard'); ?>
            <div class="text-xs text-gray-500 uppercase mt-6 mb-2 px-2 font-semibold sidebar-text">Finanças</div>
            <?php get_nav_item('gastos.php', 'chart-bar', 'Controle de Gastos'); ?>
            <?php get_nav_item('contas.php', 'receipt', 'Contas a Pagar'); ?>
            <?php get_nav_item('assinaturas.php', 'files', 'Assinaturas'); ?>
            <?php get_nav_item('cartoes.php', 'credit-card', 'Cartões'); ?>
            <div class="text-xs text-gray-500 uppercase mt-6 mb-2 px-2 font-semibold sidebar-text">Segurança</div>
            <?php get_nav_item('senhas.php', 'key', 'Senhas'); ?>
            <?php get_nav_item('autenticadores.php', 'device-mobile-camera', 'Autenticadores'); ?>
            <div class="text-xs text-gray-500 uppercase mt-6 mb-2 px-2 font-semibold sidebar-text">Organização</div>
            <?php get_nav_item('grupos.php', 'folder-simple', 'Grupos'); ?>
            <?php get_nav_item('pessoas.php', 'users', 'Pessoas'); ?>
            <?php get_nav_item('logs.php', 'terminal-window', 'Logs'); ?>
        </nav>

        <div class="flex-shrink-0">
            <div class="border-t border-gray-700 my-4"></div>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2.5 rounded-md text-red-400 hover:bg-gray-700 hover:text-white transition-colors duration-200 justify-center md:justify-start mt-1">
                <i class="ph-fill ph-sign-out text-xl"></i>
                <span class="font-medium sidebar-text">Sair</span>
            </a>
        </div>
    </aside>

    <div id="main-content" class="flex-1 flex flex-col md:ml-64 h-full">
        <header class="md:hidden sticky top-0 bg-gray-800/80 backdrop-blur-sm p-2 flex items-center justify-between z-20 border-b border-gray-700 flex-shrink-0">
            <button onclick="toggleMobileMenu()" class="p-2 text-gray-300 rounded-md hover:bg-gray-700">
                <i class="ph-fill ph-list text-2xl"></i>
            </button>
            <span class="font-bold text-lg text-white"><?= htmlspecialchars($pageTitle) ?></span>
            <div class="w-10"></div>
        </header>

        <main class="flex-1 p-4 sm:p-6 md:p-8">
<?php
}

function render_footer() {
?>
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');

    function toggleMobileMenu() {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }

    function setDesktopSidebarState(collapsed) {
        if (collapsed) {
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
            mainContent.classList.remove('md:ml-64');
            mainContent.classList.add('md:ml-20');
            sidebarTexts.forEach(text => text.classList.add('hidden'));
        } else {
            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');
            mainContent.classList.remove('md:ml-20');
            mainContent.classList.add('md:ml-64');
            sidebarTexts.forEach(text => text.classList.remove('hidden'));
        }
    }

    function toggleDesktopSidebar() {
        if (window.innerWidth >= 768) {
            const isCollapsed = sidebar.classList.contains('w-20');
            setDesktopSidebarState(!isCollapsed);
            localStorage.setItem('sidebarState', !isCollapsed ? 'collapsed' : 'expanded');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth >= 768) {
            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'collapsed') {
                setDesktopSidebarState(true);
            } else {
                setDesktopSidebarState(false);
            }
        }
    });
</script>

</body>
</html>
<?php
}
?>