const CACHE_PREFIX = '9dot-campaign-os-shell-';
const CACHE_VERSION = 'v1';
const CACHE_NAME = `${CACHE_PREFIX}${CACHE_VERSION}`;

const APP_SHELL = [
    '/offline.html',
    '/images/9dot-logo.png',
    '/icons/pwa-192.png',
    '/icons/pwa-512.png',
    '/icons/apple-touch-icon.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith(CACHE_PREFIX) && key !== CACHE_NAME)
                    .map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

const isPrivatePath = (pathname) => [
    '/admin',
    '/api',
    '/livewire',
    '/storage',
].some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));

const isSafeStaticAsset = (pathname) => (
    pathname.startsWith('/build/')
    || pathname.startsWith('/css/filament/')
    || pathname.startsWith('/js/filament/')
    || pathname.startsWith('/icons/')
    || pathname === '/images/9dot-logo.png'
    || pathname === '/favicon.ico'
);

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html')),
        );

        return;
    }

    if (isPrivatePath(url.pathname) || ! isSafeStaticAsset(url.pathname)) {
        return;
    }

    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(request).then((networkResponse) => {
                if (! networkResponse.ok || networkResponse.type !== 'basic') {
                    return networkResponse;
                }

                const responseToCache = networkResponse.clone();

                caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));

                return networkResponse;
            });
        }),
    );
});
