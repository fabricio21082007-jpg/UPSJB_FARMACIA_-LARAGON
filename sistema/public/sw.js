// Service worker de SJB: archivos estáticos desde caché y página offline cuando no hay internet.
// Las páginas del sistema (con sesión y datos) NUNCA se guardan en caché.
const CACHE = 'sjb-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/icons/icon-192.png', '/icons/icon-512.png'];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)));
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;                       // formularios (POST) pasan directo

  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;        // solo recursos de nuestro sitio

  // Navegación entre páginas: siempre a la red; si no hay internet, página offline
  if (req.mode === 'navigate') {
    event.respondWith(fetch(req).catch(() => caches.match(OFFLINE_URL)));
    return;
  }

  // CSS, JS, imágenes y fuentes: caché primero y se actualiza en segundo plano
  if (['style', 'script', 'image', 'font'].includes(req.destination)) {
    event.respondWith(
      caches.match(req).then((cached) => {
        const red = fetch(req)
          .then((res) => {
            if (res && res.status === 200) {
              const copia = res.clone();
              caches.open(CACHE).then((c) => c.put(req, copia));
            }
            return res;
          })
          .catch(() => cached);
        return cached || red;
      })
    );
  }
});
