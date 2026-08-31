const CACHE_NAME = 'tht-bni-v2';
const OFFLINE_URL = '/le-chuyen-giao';
const BNI_PATH = /^(?:\/[^/]+)?\/le-chuyen-giao(?:\/|$)/i;
const PRIVATE_PATH = /\/le-chuyen-giao\/(?:dang-nhap|dang-xuat|thu-moi\/[^/]+)(?:\/|$)/i;
const CACHEABLE_DESTINATIONS = new Set(['audio', 'font', 'image', 'script', 'style', 'video']);

const isBniPath = (pathname) => BNI_PATH.test(pathname);

const isPrivatePath = (pathname) => PRIVATE_PATH.test(pathname);

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll([
                '/bni-icon-192x192.png',
                '/bni-icon-512x512.png',
                '/bni-logo-red.svg',
            ]))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith('tht-bni-') && key !== CACHE_NAME)
                    .map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate' && isBniPath(url.pathname) && !isPrivatePath(url.pathname)) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const copy = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }

                    return response;
                })
                .catch(async () => (await caches.match(request)) || caches.match(OFFLINE_URL)),
        );

        return;
    }

    if (!CACHEABLE_DESTINATIONS.has(request.destination)) {
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) {
                return cached;
            }

            return fetch(request).then((response) => {
                if (response.ok) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                }

                return response;
            });
        }),
    );
});
