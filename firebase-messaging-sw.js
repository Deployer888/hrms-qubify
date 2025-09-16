console.log('Service worker loaded');

try {
    importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
    console.log('Firebase app script loaded successfully');
} catch (error) {
    console.error('Failed to load Firebase app script:', error);
}

try {
    importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
    console.log('Firebase messaging script loaded successfully');
} catch (error) {
    console.error('Failed to load Firebase messaging script:', error);
}

if (typeof firebase !== 'undefined') {
    const firebaseConfig = {
        apiKey: "AIzaSyA433JT-E9RICNsvrqNn-8ORBL902kL2Qw",
        authDomain: "qubifyhrm.firebaseapp.com",
        projectId: "qubifyhrm",
        storageBucket: "qubifyhrm.firebasestorage.app",
        messagingSenderId: "830057480358",
        appId: "1:830057480358:web:275d7c3da0b7b6b54fb467",
        measurementId: "G-SQBR2GK49N"
    };

    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    messaging.onBackgroundMessage((payload) => {
        console.log('Received background message:', payload);

        const { title, body } = payload.notification;
        self.registration.showNotification(title, {
            body,
            icon: '/icon.png', // Optional notification icon
        });
    });
}
