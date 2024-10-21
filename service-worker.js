const CACHE_NAME = 'brainy-cache-v1';
const urlsToCache = [
  '/',
  '/index.html',
  '/calendar.php',
  '/calendarScript.js',
  '/calendarStyle.css',
  '/profile_icon.png',
  '/home.png',
  '/calendar.png',
  '/board.png',
  'https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        return response || fetch(event.request);
      })
  );
});
