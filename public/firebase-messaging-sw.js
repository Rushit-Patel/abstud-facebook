importScripts("https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyDFauyP2JDBm8JYg_HYGXbcRPxJ3RibcxY",
    authDomain: "abstud-erp.firebaseapp.com",
    projectId: "abstud-erp",
    storageBucket: "abstud-erp.firebasestorage.app",
    messagingSenderId: "985069216195",
    appId: "1:985069216195:web:01e0d020c7169c80e8dd76"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const data = payload.data || payload.notification;
    const notificationTitle = data.title || "New Notification";
    const notificationOptions = {
        body: data.body,
        icon: '/firebase-icon.png',
        data: {
            link: data.link
        }
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const link = event.notification.data.link || '/';
    event.waitUntil(clients.openWindow(link));
});
