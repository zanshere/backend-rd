import './smooth-scroll';
import '../css/app.css';
import {
    Star, Trophy, Crown, Check, Lock, Bookmark, Phone, MessageCircle,
    Mail, ChevronRight, Loader2, AlertCircle, CheckCircle2, X,
    Send, Paperclip, Download, CheckCheck, Plus, MessageSquare
} from 'lucide';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Register Lucide icons globally
window.Lucide = {
    Star, Trophy, Crown, Check, Lock, Bookmark, Phone, MessageCircle,
    Mail, ChevronRight, Loader2, AlertCircle, CheckCircle2, X,
    Send, Paperclip, Download, CheckCheck, Plus, MessageSquare
};

window.Pusher = Pusher;

// Initialize Laravel Echo for real-time features
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || process.env.MIX_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Authorization': `Bearer ${getAuthToken()}`
        },
    },
    authEndpoint: '/broadcasting/auth',
});

// Function to get authentication token
function getAuthToken() {
    // Jika menggunakan Laravel Sanctum atau token-based auth
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return token || '';
}

// Authentication for Presence Channels
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('Pusher connected successfully for real-time messaging');
});

window.Echo.connector.pusher.connection.bind('error', function(err) {
    console.error('Pusher connection error:', err);
});

// Global real-time event listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeGlobalEchoListeners();
});

function initializeGlobalEchoListeners() {
    // Global notification listener
    window.Echo.private(`App.Models.User.${getCurrentUserId()}`)
        .notification((notification) => {
            console.log('Received notification:', notification);
            showBrowserNotification(notification);
        });
}

function getCurrentUserId() {
    // Ambil user ID dari meta tag atau global variable
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    return userIdMeta ? userIdMeta.getAttribute('content') : null;
}

function showBrowserNotification(notification) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notification.title || 'New Notification', {
            body: notification.message || 'You have a new notification',
            icon: '/images/notification-icon.png',
            tag: 'chat-notification'
        });
    }
}

// Enhanced notification permission request
if ('Notification' in window) {
    if (Notification.permission === 'default') {
        // Only ask for permission when user interacts with chat
        document.addEventListener('click', function requestNotificationPermission() {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notification permission granted');
                    document.removeEventListener('click', requestNotificationPermission);
                }
            });
        }, { once: true });
    }
}

// Listen for Livewire events
Livewire.on('new-notification', (data) => {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(data.title, {
            body: data.message,
            icon: '/images/notification-icon.png',
            tag: 'livewire-notification'
        });
    }
});

// Initialize Lucide icons after Livewire updates
document.addEventListener('livewire:init', () => {
    // Icons will be initialized automatically by Lucide
    if (window.Lucide && window.Lucide.createIcons) {
        window.Lucide.createIcons();
    }
});

document.addEventListener('livewire:navigated', () => {
    // Re-initialize icons after navigation
    if (window.Lucide && window.Lucide.createIcons) {
        window.Lucide.createIcons();
    }

    // Re-initialize Echo listeners after navigation
    setTimeout(initializeGlobalEchoListeners, 100);
});

// Handle page visibility for real-time optimization
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, refresh any real-time data
        Livewire.dispatch('page-became-visible');
    }
});

// Export for module usage
export { Echo, Pusher };
