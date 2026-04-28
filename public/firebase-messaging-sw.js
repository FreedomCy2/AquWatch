/* eslint-disable no-undef */
// Basic service worker required for Firebase web push token registration.
// Your Firebase teammate can extend this file to handle background messages.
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

self.addEventListener('install', () => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

firebase.initializeApp({
  apiKey: "AIzaSyC3jvlbTIq2NY6ksoIoUsw5Xj0EXE1tlds",
  authDomain: "planning-with-ai-c5c57.firebaseapp.com",
  projectId: "planning-with-ai-c5c57",
  storageBucket: "planning-with-ai-c5c57.firebasestorage.app",
  messagingSenderId: "428551261197",
  appId: "1:428551261197:android:337a13a9eb1d2887dad944"
});

const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
  self.registration.showNotification(payload.notification?.title || 'AquWatch', {
    body: payload.notification?.body || '',
    icon: '/favicon.ico'
  });
});
