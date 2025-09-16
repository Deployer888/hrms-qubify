// Import the necessary Firebase modules
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// Your Firebase configuration object
const firebaseConfig = {
  apiKey: "AIzaSyB7TB0o6HZpVDwhFywKIoFM3R6fzZvvJcs",
  authDomain: "hrms-888.firebaseapp.com",
  projectId: "hrms-888",
  storageBucket: "hrms-888.appspot.com",
  messagingSenderId: "381127866106",
  appId: "1:381127866106:web:8cc50e6d9d006e521abb43",
  measurementId: "G-GW357QQ39X"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Request permission and get token
async function requestPermission() {
  try {
    await Notification.requestPermission();
    const token = await getToken(messaging, { vapidKey: 'BCEh-MxLoedY9yO23vZNa4r62YtclH_b_bLfhRpaWhj_EFZ0kgMWVZN5IKqFzUXkiTBWhW4Cq554e94-wVeKeB4' });
    console.log('FCM Token:', token);
    // Send the token to your server
    await axios.post('/api/save-fcm-token', { token });
  } catch (error) {
    console.error('Error getting FCM token:', error);
  }
}

onMessage(messaging, (payload) => {
  console.log('Message received. ', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon
  };

  if (Notification.permission === 'granted') {
    new Notification(notificationTitle, notificationOptions);
  }
});

// Call requestPermission function to ask for notification permissions
requestPermission();
