self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('atm-cache-v1').then(cache => {
            return cache.addAll([
                '/',
                '/index.html',
                'Atm.jpg',
                '/manifest.json'
            ]);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});