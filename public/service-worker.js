const CACHE_NAME = 'assis-pessoal-cache-v1';
// Lista de arquivos essenciais para o "casco" do aplicativo.
const urlsToCache = [
  '/',
  '/index.php',
  '/dashboard.php',
  '/senhas.php',
  '/assinaturas.php',
  '/contas.php',
  '/cartoes.php',
  '/autenticadores.php',
  '/grupos.php',
  '/pessoas.php',
  '/logs.php',
  // Adicione aqui os caminhos para seus principais ícones e CSS se tiver algum customizado
  'https://cdn.tailwindcss.com',
  'https://unpkg.com/@phosphor-icons/web'
];

// Evento de Instalação: Salva os arquivos essenciais em cache.
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aberto');
        return cache.addAll(urlsToCache);
      })
  );
});

// Evento de Ativação: Limpa caches antigos.
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Evento de Fetch: Intercepta as requisições.
// Tenta buscar no cache primeiro. Se não encontrar, busca na rede.
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Se encontrou no cache, retorna do cache
        if (response) {
          return response;
        }
        // Se não, busca na rede
        return fetch(event.request);
      }
    )
  );
});