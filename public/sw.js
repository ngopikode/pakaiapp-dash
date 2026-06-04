self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', event => {
    // A dummy fetch event handler is required for a PWA to be installable
    event.respondWith(fetch(event.request));
});
