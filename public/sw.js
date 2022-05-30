const CACHE_VERSION = '1';
const CACHE_FILES = [

];

self.addEventListener('install', event => {
    caches.open(CACHE_VERSION).then(cache => {
        cache.addAll(CACHE_FILES);
    })
    self.skipWaiting();
});
self.addEventListener('fetch', event => {
    const requestURL = new URL(event.request.url);
    // no cache request
    if (requestURL.href.includes("__nc__")) {
        event.respondWith(fetch(event.request));
        return;
    }
    // refresh cash request
    if (requestURL.href.includes("__rc__")) {
        event.respondWith(
            fetch(event.request).then(response => {
                caches.open(CACHE_VERSION).then(cache => {
                    let cacheUrl = requestURL.href;
                    cacheUrl = cacheUrl.replace('__rc__', '');
                    if (cacheUrl.charAt(cacheUrl.length - 1) == '?') {
                        cacheUrl = cacheUrl.substring(0, cacheUrl.length - 1);
                    }
                    cache.put(cacheUrl, response);
                });
                return response.clone()
            })
        );
        return;
    }
    event.respondWith(
        caches.match(event.request).then(response => response || fetch(event.request))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => Promise.all(
            cacheNames.filter(cacheName => cacheName !== CACHE_VERSION)
            .map(cacheName => caches.delete(cacheName))
        ))
    );
});