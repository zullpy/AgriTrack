const CACHE_NAME = 'agritrack-v2';
const PRECACHE_ASSETS = [
    '/',
    '/dashboard',
    '/kalender-hst',
    '/steps',
    '/data-obat',
    '/data-obat/tambah',
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

// Fetch: Network-first for HTML pages with cache fallback; Stale-while-revalidate for assets
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
                        || (await caches.match('/data-obat'));
                })
        );
        return;
    }

    // Static assets (CSS, JS, Fonts, Images)
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            const fetchPromise = fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Network failed, nothing extra needed if cachedResponse is returned below
                });

            return cachedResponse || fetchPromise;
        })
    );
});
