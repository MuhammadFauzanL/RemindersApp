const CACHE_NAME = "reminderapps-v2";
const urlsToCache = [
    "/",
    "/login",
    "/register",
    "/dashboard",
];

self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache).catch(err => {
                console.warn("[SW] Failed to cache some assets:", err);
            });
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => cacheName !== CACHE_NAME)
                    .map(cacheName => caches.delete(cacheName))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", event => {
    // Skip non-GET requests
    if (event.request.method !== "GET") return;
    
    // Skip cross-origin requests
    if (new URL(event.request.url).origin !== self.location.origin) return;

    event.respondWith(
        caches.match(event.request).then(cachedResponse => {
            // If we have a cached response, return it
            if (cachedResponse) {
                return cachedResponse;
            }

            // Otherwise, try to fetch from network
            return fetch(event.request).then(response => {
                // Only cache successful responses
                if (!response || response.status !== 200 || response.type !== "basic") {
                    return response;
                }

                // Clone the response
                const responseToCache = response.clone();

                // Cache the response for future use
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, responseToCache);
                });

                return response;
            }).catch(() => {
                // Network failed - for navigation requests, return a cached page
                if (event.request.mode === "navigate") {
                    return caches.match("/") || caches.match("/login");
                }
                // For other requests, return nothing (will show error)
                return new Response("Offline", { status: 503, statusText: "Service Unavailable" });
            });
        })
    );
});
