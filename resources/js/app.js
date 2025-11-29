import './smooth-scroll';
import '../css/app.css';
import {
    Star, Trophy, Crown, Check, Lock, Bookmark, Phone, MessageCircle,
    Mail, ChevronRight, Loader2, AlertCircle, CheckCircle2, X
} from 'lucide';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Register Lucide icons globally
window.Lucide = {
    Star, Trophy, Crown, Check, Lock, Bookmark, Phone, MessageCircle,
    Mail, ChevronRight, Loader2, AlertCircle, CheckCircle2, X
};

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

// Initialize Lucide icons after Livewire updates
document.addEventListener('livewire:init', () => {
    // Icons will be initialized automatically by Lucide
});

document.addEventListener('livewire:navigated', () => {
    // Re-initialize icons after navigation
    if (window.LucideIcons) {
        window.LucideIcons.replace();
    }
});
