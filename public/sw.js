// ── Service Worker — Mini Ecommerce PWA ──────────────────────────
const CACHE_NAME    = 'toko-admin-v1';
const OFFLINE_URL   = '/offline';

// File yang di-cache saat install
const PRECACHE = [
    '/',
    '/dashboard',
    '/css/admin.css',
    '/offline',
];

// ── Install ───────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE).catch(() => {
                // Abaikan error jika ada file yang tidak bisa di-cache
            });
        })
    );
    self.skipWaiting();
});

// ── Activate ──────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ── Fetch Strategy ────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const req = event.request;

    // Skip non-GET & browser extension requests
    if (req.method !== 'GET') return;
    if (!req.url.startsWith('http')) return;

    const url = new URL(req.url);

    // Static assets — Cache First
    if (
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/pwa/') ||
        url.pathname.match(/\.(png|jpg|jpeg|gif|webp|ico|svg|woff|woff2|ttf|js)$/)
    ) {
        event.respondWith(
            caches.match(req).then(cached => cached || fetch(req).then(resp => {
                if (resp.ok) {
                    const clone = resp.clone();
                    caches.open(CACHE_NAME).then(c => c.put(req, clone));
                }
                return resp;
            }))
        );
        return;
    }

    // HTML pages — Network First, fallback to cache, fallback to offline
    if (req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req)
                .then(resp => {
                    if (resp.ok) {
                        const clone = resp.clone();
                        caches.open(CACHE_NAME).then(c => c.put(req, clone));
                    }
                    return resp;
                })
                .catch(() =>
                    caches.match(req).then(cached =>
                        cached || caches.match(OFFLINE_URL)
                    )
                )
        );
        return;
    }
});

// ── Push Notification ─────────────────────────────────────────────
self.addEventListener('push', event => {
    let data = { title: 'Pesanan Baru! 🛒', body: 'Ada pesanan baru masuk.', url: '/orders' };

    try {
        data = { ...data, ...event.data.json() };
    } catch (e) {}

    const options = {
        body: data.body,
        icon: '/pwa/icon-192.png',
        badge: '/pwa/icon-72.png',
        vibrate: [200, 100, 200],
        data: { url: data.url },
        actions: [
            { action: 'view',    title: '👁 Lihat Pesanan' },
            { action: 'dismiss', title: '✕ Tutup' }
        ],
        requireInteraction: true,
        tag: 'new-order',
        renotify: true,
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// ── Notification Click ────────────────────────────────────────────
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const url = event.notification.data?.url || '/orders';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            // Jika sudah ada tab terbuka, fokus ke sana
            for (const client of list) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            // Buka tab baru
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});

// ── Background Sync (untuk kirim data saat offline) ───────────────
self.addEventListener('sync', event => {
    if (event.tag === 'sync-orders') {
        event.waitUntil(syncPendingOrders());
    }
});

async function syncPendingOrders() {
    // Placeholder untuk sync data offline
    console.log('[SW] Background sync: orders');
}
