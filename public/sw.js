const CACHE_NAME = 'agritrack-v16';
const PRECACHE_ASSETS = [
    '/',
    '/dashboard',
    '/tanaman-aktif',
    '/kalender-hst',
    '/kalender-hss',
    '/tanaman-katalog',
    '/data-obat',
    '/data-obat/tambah',
    '/stok-obat',
    '/steps',
    '/steps/fermentasi-pupuk',
    '/steps/pengolahan-tanah',
    '/steps/penanaman-bibit',
    '/steps/catatan',
    '/keuangan',
    '/config-fonnte',
    '/images/icon.png',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/dexie@4.0.11/dist/dexie.min.js',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// Install: precache essential shell assets and Vite bundle assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            // 1. Precache main navigation pages & libraries
            await Promise.allSettled(
                PRECACHE_ASSETS.map(async (url) => {
                    try {
                        const response = await fetch(url);
                        if (response.ok) {
                            await cache.put(url, response);
                        }
                    } catch (err) {
                        console.warn('Precache failed for asset:', url, err);
                    }
                })
            );

            // 2. Precache compiled Vite assets directly from manifest
            try {
                const manifestRes = await fetch('/build/manifest.json');
                if (manifestRes.ok) {
                    await cache.put('/build/manifest.json', manifestRes.clone());
                    const manifest = await manifestRes.json();
                    const viteAssetUrls = [];
                    for (const key in manifest) {
                        if (manifest[key].file) {
                            viteAssetUrls.push('/build/' + manifest[key].file);
                        }
                        if (Array.isArray(manifest[key].css)) {
                            manifest[key].css.forEach(cssFile => {
                                viteAssetUrls.push('/build/' + cssFile);
                            });
                        }
                    }
                    await Promise.allSettled(
                        viteAssetUrls.map(async (assetUrl) => {
                            try {
                                const aRes = await fetch(assetUrl);
                                if (aRes.ok) {
                                    await cache.put(assetUrl, aRes);
                                }
                            } catch (e) {
                                console.warn('Failed to precache Vite asset:', assetUrl, e);
                            }
                        })
                    );
                }
            } catch (mErr) {
                console.warn('Could not precache Vite manifest assets:', mErr);
            }
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
                    const reqUrl = new URL(request.url);

                    // 1. Coba cari match langsung dengan request (abaikan query params)
                    let cachedResponse = await caches.match(request, { ignoreSearch: true });
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // 2. Coba cari match berdasarkan pathname (misal /keuangan, /kalender-hst, dll)
                    cachedResponse = await caches.match(reqUrl.pathname, { ignoreSearch: true });
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // 3. Jika buka root '/', kembalikan /dashboard
                    if (reqUrl.pathname === '/' || reqUrl.pathname === '') {
                        const dashMatch = await caches.match('/dashboard');
                        if (dashMatch) return dashMatch;
                    }

                    // 4. Jika sub-halaman kalender, katalog, obat, atau steps
                    if (reqUrl.pathname.startsWith('/kalender-hst')) {
                        const hstMatch = await caches.match('/kalender-hst');
                        if (hstMatch) return hstMatch;
                    }
                    if (reqUrl.pathname.startsWith('/kalender-hss')) {
                        const hssMatch = await caches.match('/kalender-hss');
                        if (hssMatch) return hssMatch;
                    }
                    if (reqUrl.pathname.startsWith('/tanaman-katalog')) {
                        const katMatch = await caches.match('/tanaman-katalog');
                        if (katMatch) return katMatch;
                    }
                    if (reqUrl.pathname.startsWith('/data-obat') || reqUrl.pathname.startsWith('/stok-obat')) {
                        const obatMatch = await caches.match('/data-obat');
                        if (obatMatch) return obatMatch;
                    }
                    if (reqUrl.pathname.startsWith('/steps')) {
                        const stepMatch = await caches.match('/steps');
                        if (stepMatch) return stepMatch;
                    }

                    // 5. Fallback ke shell yang tersedia jika URL belum pernah di-cache
                    return (await caches.match('/dashboard'))
                        || (await caches.match('/tanaman-aktif'))
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
