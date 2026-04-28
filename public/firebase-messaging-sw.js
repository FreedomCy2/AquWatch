/* eslint-disable no-undef */
// Basic service worker required for Firebase web push token registration.
// Your Firebase teammate can extend this file to handle background messages.
self.addEventListener('install', () => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

