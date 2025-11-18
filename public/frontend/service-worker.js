const CACHE_NAME = 'sign-language-pwa-v1';
const urlsToCache = [
    '/frontend/',
    '/frontend/index.html',
    '/frontend/styles.css',
    '/frontend/app.js',
    '/frontend/manifest.json',
    '/frontend/icon-192.png',
    '/frontend/icon-512.png',
    '/frontend/icon-144.png'
];

// Install event - cache resources
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Service Worker: Caching files');
                return cache.addAll(urlsToCache);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // API requests - network first, then cache (skip caching redirected responses)
    if (event.request.url.includes('/api/')) {
        event.respondWith(
            fetch(event.request, { redirect: 'follow', credentials: 'same-origin' })
                .then((response) => {
                    // If the response was redirected, don't cache it.
                    if (response && response.redirected) {
                        console.warn('[SW] Skipping cache of redirected API response for', event.request.url, '->', response.url);
                        return response;
                    }

                    // Clone the response
                    const responseClone = response.clone();

                    // Cache the response
                    caches.open(CACHE_NAME).then((cache) => {
                        try { cache.put(event.request, responseClone); } catch (e) { console.warn('[SW] cache.put failed', e); }
                    });

                    return response;
                })
                .catch(async () => {
                    // If network fails, try cache but ignore redirected cached responses
                    let cached = await caches.match(event.request);
                    if (cached && cached.redirected) {
                        try { const c = await caches.open(CACHE_NAME); await c.delete(event.request); } catch(e) { console.warn('[SW] failed to delete redirected cache', e); }
                        cached = null;
                    }
                    return cached;
                })
        );
        return;
    }

    // Static resources - cache first, then network
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                // If cached response is a redirected response, remove it and fall back to network
                if (response && response.redirected) {
                    caches.open(CACHE_NAME).then((cache) => {
                        try { cache.delete(event.request); console.warn('[SW] Removed redirected cached static resource', event.request.url); } catch(e) { console.warn('[SW] failed to delete redirected cache', e); }
                    });
                    response = null;
                }

                // Return cached version or fetch from network
                return response || fetch(event.request, { redirect: 'follow', credentials: 'same-origin' })
                    .then((fetchResponse) => {
                        // If the network response is redirected, do not cache it.
                        if (fetchResponse && fetchResponse.redirected) {
                            console.warn('[SW] Network response was redirected for', event.request.url, '->', fetchResponse.url);
                            return fetchResponse;
                        }

                        // Cache the fetched response
                        return caches.open(CACHE_NAME).then((cache) => {
                            try { cache.put(event.request, fetchResponse.clone()); } catch(e) { console.warn('[SW] cache.put failed for', event.request.url, e); }
                            return fetchResponse;
                        });
                    });
            })
            .catch(() => {
                // If both cache and network fail, return offline page
                if (event.request.destination === 'document') {
                    return caches.match('/frontend/index.html');
                }
            })
    );
});

// Background sync for offline requests
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-signs') {
        event.waitUntil(syncSigns());
    }
});

async function syncSigns() {
    try {
        // Implement background sync logic here
        console.log('Service Worker: Syncing signs...');
    } catch (error) {
        console.error('Service Worker: Sync failed:', error);
    }
}

// Push notification support
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'محول لغة الإشارة';
    const options = {
        body: data.body || 'لديك إشعار جديد',
        icon: '/frontend/icon-192.png',
        badge: '/frontend/icon-192.png',
        vibrate: [200, 100, 200],
        data: data.url || '/frontend/'
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data)
    );
});
