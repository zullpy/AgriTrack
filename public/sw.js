const CACHE_NAME = 'agritrack-v5';
const PRECACHE_ASSETS = [
    '/',
    '/dashboard',
    '/kalender-hst',
    '/steps',
    '/data-obat',
    '/data-obat/tambah',
    '/keuangan',
    '/images/icon.png',
    '/manifest.json'
];

// Install: precache essential shell assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean up outdated caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Network-first for HTML pages with cache fallback; Cache-first for images & assets
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Skip non-GET requests and non-http(s)
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // Skip API sync requests so the offline-store handles queueing
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    // Skip AJAX/XHR requests — always go to network for JSON data fetches
    const acceptHeader = request.headers.get('Accept') || '';
    const xhrHeader = request.headers.get('X-Requested-With') || '';
    if (acceptHeader.includes('application/json') || xhrHeader === 'XMLHttpRequest') {
        return;
    }

    // Navigation requests (HTML pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response && response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(async () => {
                    // Offline fallback: check cache for exact match, or fallback to main shells
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return (await caches.match('/dashboard'))
                        || (await caches.match('/kalender-hst'))
                        || (await caches.match('/data-obat'))
                        || (await caches.match('/keuangan'));
                })
        );
        return;
    }

    // Static assets & Images (including cross-origin Cloudinary images)
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request)
                .then((networkResponse) => {
                    // Cache standard 200 and opaque (cross-origin Cloudinary) image responses
                    if (networkResponse && (networkResponse.status === 200 || networkResponse.type === 'opaque')) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return networkResponse;
                })
                .catch(() => {
                    return cachedResponse || new Response('', { status: 408, statusText: 'Offline' });
                });
        })
    );
});
