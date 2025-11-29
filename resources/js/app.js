import './smooth-scroll';
import '../css/app.css';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Listen for browser notifications
Livewire.on('new-notification', (data) => {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(data.title, {
            body: data.message,
            icon: '/images/notification-icon.png'
        });
    }
});

// Request notification permission
if ('Notification' in window) {
    Notification.requestPermission();
}
