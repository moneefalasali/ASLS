// AISL PWA Service Worker
// IMPORTANT: bump these versions on deploy so clients receive the new SW and caches get updated.
// Use a build task or manually update these constants for each production deployment.
const CACHE_NAME = 'aisl-v1.2.0';
const STATIC_CACHE = 'aisl-static-v1.2.0';
const DYNAMIC_CACHE = 'aisl-dynamic-v1.2.0';

// Files to cache immediately
const STATIC_ASSETS = [
  '/',
  '/frontend/mobile-styles.css',
  '/frontend/app.js',
  '/manifest.json',
  // Add common sign images and assets
  '/storage/signs/placeholder.png',
  // Offline fallback page
  '/offline.html'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
  /^\/api\/convert-text/,
  /^\/api\/signs/,
  /^\/api\/conversations/
];

// Install event - cache static assets
self.addEventListener('install', event => {
  console.log('[SW] Installing service worker...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('[SW] Static assets cached successfully');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('[SW] Failed to cache static assets:', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  console.log('[SW] Activating service worker...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('[SW] Service worker activated');
        return self.clients.claim();
      })
  );
});

// Fetch event - serve from cache with network fallback
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Skip external requests
  if (url.origin !== location.origin) {
    return;
  }

  // Check if this is an authenticated route
  const isAuthRoute = url.pathname.match(/^\/(signs|conversations|profile|dashboard)/);
  
  // For authenticated routes, always go network-first
  if (isAuthRoute) {
    event.respondWith(networkFirst(request));
    return;
  }
  
  // Handle other types of requests
  if (isStaticAsset(request)) {
    event.respondWith(cacheFirst(request));
  } else if (isAPIRequest(request)) {
    event.respondWith(networkFirst(request));
  } else if (isPageRequest(request)) {
    event.respondWith(staleWhileRevalidate(request));
  } else {
    event.respondWith(networkFirst(request));
  }
});

// Cache strategies
async function cacheFirst(request) {
  try {
    let cachedResponse = await caches.match(request);
    // If the cached response is a redirected response (from an earlier SW run),
    // remove it and act as if it's not cached. Returning redirected responses
    // to certain navigation requests causes the browser warning seen in the
    // console: "a redirected response was used for a request whose redirect
    // mode is not 'follow'." Avoid returning such responses.
    if (cachedResponse && cachedResponse.redirected) {
      try {
        const cache = await caches.open(STATIC_CACHE);
        await cache.delete(request);
        console.warn('[SW] Removed redirected cached response for', request.url);
      } catch (e) {
        console.warn('[SW] Failed to remove redirected cached response', request.url, e);
      }
      cachedResponse = null;
    }
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // Follow redirects and include credentials for same-origin requests
    const networkResponse = await fetch(request, { redirect: 'follow', credentials: 'same-origin' });
    // Only cache non-redirected, OK responses (avoid opaqueredirect / redirected responses)
    if (networkResponse && networkResponse.ok && !networkResponse.redirected) {
      const cache = await caches.open(STATIC_CACHE);
      try {
        await cache.put(request, networkResponse.clone());
      } catch (e) {
        // Some responses (opaque, redirected) can't be cached — ignore cache errors
        console.warn('[SW] cache.put failed for', request.url, e);
      }
    }
    return networkResponse;
  } catch (error) {
    console.error('[SW] Cache first failed:', error);
    return getOfflineFallback(request);
  }
}

async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request, { 
      redirect: 'follow', 
      credentials: 'same-origin',
      // Ensure we get fresh data for authenticated routes
      cache: 'no-store'
    });
    
    // If we get redirected to login, let the browser handle it
    if (networkResponse.redirected && networkResponse.url.includes('/login')) {
      return Response.redirect(networkResponse.url, 302);
    }
    
    // For successful responses that aren't redirects, we can cache
    if (networkResponse && networkResponse.ok && !networkResponse.redirected) {
      const cache = await caches.open(DYNAMIC_CACHE);
      try {
        await cache.put(request, networkResponse.clone());
      } catch (e) {
        console.warn('[SW] cache.put failed for', request.url, e);
      }
    }
    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url);
    // If network failed, try the cache but skip any cached redirected responses.
    let cachedResponse = await caches.match(request);
    if (cachedResponse && cachedResponse.redirected) {
      try {
        const cache = await caches.open(DYNAMIC_CACHE);
        await cache.delete(request);
        console.warn('[SW] Removed redirected cached response during fallback for', request.url);
      } catch (e) {
        console.warn('[SW] Failed to remove redirected cached response', request.url, e);
      }
      cachedResponse = null;
    }
    if (cachedResponse) {
      return cachedResponse;
    }
    return getOfflineFallback(request);
  }
}

async function staleWhileRevalidate(request) {
  const cache = await caches.open(DYNAMIC_CACHE);
  let cachedResponse = await cache.match(request);
  // Ignore cached redirected responses (avoid returning them for navigation)
  if (cachedResponse && cachedResponse.redirected) {
    try {
      await cache.delete(request);
      console.warn('[SW] Removed redirected cached response in staleWhileRevalidate for', request.url);
    } catch (e) {
      console.warn('[SW] Failed to remove redirected cached response', request.url, e);
    }
    cachedResponse = null;
  }
  
  const fetchPromise = fetch(request, { redirect: 'follow', credentials: 'same-origin' }).then(networkResponse => {
    if (networkResponse && networkResponse.ok && !networkResponse.redirected) {
      try { cache.put(request, networkResponse.clone()); } catch(e) { console.warn('[SW] cache.put failed for', request.url, e); }
    } else if (networkResponse && networkResponse.redirected) {
      // If the network returned a redirected response for a navigation/page
      // request, let the browser handle the redirect. We don't cache such
      // responses and avoid returning them from the SW for navigation-type
      // requests to prevent the console warning.
      console.warn('[SW] Network response was redirected for', request.url, '->', networkResponse.url);
    }
    return networkResponse;
  }).catch(() => cachedResponse);
  
  return cachedResponse || fetchPromise;
}

// Helper functions
function isStaticAsset(request) {
  const url = new URL(request.url);
  return url.pathname.includes('/frontend/') || 
         url.pathname.includes('/storage/') ||
         url.pathname.endsWith('.css') ||
         url.pathname.endsWith('.js') ||
         url.pathname.endsWith('.png') ||
         url.pathname.endsWith('.jpg') ||
         url.pathname.endsWith('.svg') ||
         url.pathname.endsWith('.ico');
}

function isAPIRequest(request) {
  const url = new URL(request.url);
  return url.pathname.startsWith('/api/') ||
         API_CACHE_PATTERNS.some(pattern => pattern.test(url.pathname));
}

function isPageRequest(request) {
  const url = new URL(request.url);
  return request.headers.get('accept')?.includes('text/html') ||
         url.pathname === '/' ||
         url.pathname.startsWith('/signs') ||
         url.pathname.startsWith('/conversations') ||
         url.pathname.startsWith('/profile');
}

async function getOfflineFallback(request) {
  const url = new URL(request.url);
  
  // Return offline page for navigation requests
  if (request.headers.get('accept')?.includes('text/html')) {
    const offlinePage = await caches.match('/offline.html');
    if (offlinePage) {
      return offlinePage;
    }
  }
  
  // Return placeholder for images
  if (request.headers.get('accept')?.includes('image/')) {
    const placeholder = await caches.match('/storage/signs/placeholder.png');
    if (placeholder) {
      return placeholder;
    }
  }
  
  // Return generic offline response
  return new Response(
    JSON.stringify({
      error: 'Offline',
      message: 'هذا المحتوى غير متاح في وضع عدم الاتصال'
    }),
    {
      status: 503,
      statusText: 'Service Unavailable',
      headers: {
        'Content-Type': 'application/json',
        'Cache-Control': 'no-cache'
      }
    }
  );
}

// Background sync for offline actions
self.addEventListener('sync', event => {
  console.log('[SW] Background sync triggered:', event.tag);
  
  if (event.tag === 'translate-text') {
    event.waitUntil(syncTranslations());
  } else if (event.tag === 'send-message') {
    event.waitUntil(syncMessages());
  }
});

async function syncTranslations() {
  try {
    // Get pending translations from IndexedDB
    const pendingTranslations = await getPendingTranslations();
    
    for (const translation of pendingTranslations) {
      try {
        const response = await fetch('/api/convert-text', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(translation.data)
        });
        
        if (response.ok) {
          await removePendingTranslation(translation.id);
          console.log('[SW] Synced translation:', translation.id);
        }
      } catch (error) {
        console.error('[SW] Failed to sync translation:', error);
      }
    }
  } catch (error) {
    console.error('[SW] Background sync failed:', error);
  }
}

async function syncMessages() {
  // Similar implementation for syncing messages
  console.log('[SW] Syncing messages...');
}

// Push notifications
self.addEventListener('push', event => {
  console.log('[SW] Push notification received');
  
  const options = {
    body: 'لديك رسالة جديدة في AISL',
    icon: '/frontend/app-icon-192.png',
    badge: '/frontend/app-icon-192.png',
    tag: 'aisl-notification',
    data: {
      url: '/conversations'
    },
    actions: [
      {
        action: 'open',
        title: 'فتح',
        icon: '/frontend/action-open.png'
      },
      {
        action: 'dismiss',
        title: 'إغلاق',
        icon: '/frontend/action-close.png'
      }
    ],
    requireInteraction: false,
    silent: false
  };
  
  if (event.data) {
    try {
      const data = event.data.json();
      options.body = data.message || options.body;
      options.data = { ...options.data, ...data };
    } catch (error) {
      console.error('[SW] Failed to parse push data:', error);
    }
  }
  
  event.waitUntil(
    self.registration.showNotification('AISL', options)
  );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
  console.log('[SW] Notification clicked:', event.action);
  
  event.notification.close();
  
  if (event.action === 'dismiss') {
    return;
  }
  
  const url = event.notification.data?.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then(clientList => {
        // Try to focus existing window
        for (const client of clientList) {
          if (client.url.includes(url) && 'focus' in client) {
            return client.focus();
          }
        }
        
        // Open new window
        if (clients.openWindow) {
          return clients.openWindow(url);
        }
      })
  );
});

// Utility functions for IndexedDB operations
async function getPendingTranslations() {
  // Placeholder - implement IndexedDB operations
  return [];
}

async function removePendingTranslation(id) {
  // Placeholder - implement IndexedDB operations
  console.log('Removing pending translation:', id);
}

// Performance monitoring
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'GET_VERSION') {
    event.ports[0].postMessage({ version: CACHE_NAME });
  }
});

console.log('[SW] Service worker script loaded successfully —', CACHE_NAME);
