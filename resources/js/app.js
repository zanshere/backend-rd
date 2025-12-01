import './smooth-scroll';
import '../css/app.css';

// Import Echo dan Pusher
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Import hanya icon yang digunakan
import {
    Star, Trophy, Crown, Check, Lock, Bookmark, Phone, MessageCircle,
    Mail, ChevronRight, Loader2, AlertCircle, CheckCircle2, X,
    Send, Paperclip, Download, CheckCheck, Plus, MessageSquare,
    User, ShoppingBag, FileText, Settings, LogOut, Home,
    Bell, Search, Menu, Eye, EyeOff, Calendar, Clock,
    Users, Package, DollarSign, CreditCard, Truck,
    Edit, Trash2, Copy, Share, Upload, Camera, Image, File, Folder,
    ChevronLeft, ChevronDown, ChevronUp,
    RefreshCw, Power,
    ShoppingCart, Tag,
    Mail as MailIcon,
    Image as ImageIcon,
    Circle,
    MapPin,
    Smartphone, Monitor, Printer,
    Briefcase, Building, Key,
    Shield,
    UserCheck, UserPlus, UserX
} from 'lucide';

// Register Pusher dan Echo ke window global
window.Pusher = Pusher;
window.Echo = Echo;

// Simple Lucide icons implementation
function initializeLucideIcons() {
    const elements = document.querySelectorAll('[data-lucide]');
    elements.forEach(el => {
        const iconName = el.getAttribute('data-lucide');
        const iconMap = {
            'star': Star,
            'trophy': Trophy,
            'crown': Crown,
            'check': Check,
            'lock': Lock,
            'bookmark': Bookmark,
            'phone': Phone,
            'message-circle': MessageCircle,
            'mail': Mail,
            'chevron-right': ChevronRight,
            'loader2': Loader2,
            'alert-circle': AlertCircle,
            'check-circle-2': CheckCircle2,
            'x': X,
            'send': Send,
            'paperclip': Paperclip,
            'download': Download,
            'check-check': CheckCheck,
            'plus': Plus,
            'message-square': MessageSquare,
            'user': User,
            'shopping-bag': ShoppingBag,
            'file-text': FileText,
            'settings': Settings,
            'log-out': LogOut,
            'home': Home,
            'bell': Bell,
            'search': Search,
            'menu': Menu,
            'eye': Eye,
            'eye-off': EyeOff,
            'calendar': Calendar,
            'clock': Clock,
            'users': Users,
            'package': Package,
            'dollar-sign': DollarSign,
            'credit-card': CreditCard,
            'truck': Truck,
            'edit': Edit,
            'trash-2': Trash2,
            'copy': Copy,
            'share': Share,
            'upload': Upload,
            'camera': Camera,
            'image': Image,
            'file': File,
            'folder': Folder,
            'chevron-left': ChevronLeft,
            'chevron-down': ChevronDown,
            'chevron-up': ChevronUp,
            'refresh-cw': RefreshCw,
            'power': Power,
            'shopping-cart': ShoppingCart,
            'tag': Tag,
            'mail-icon': MailIcon,
            'image-icon': ImageIcon,
            'circle': Circle,
            'map-pin': MapPin,
            'smartphone': Smartphone,
            'monitor': Monitor,
            'printer': Printer,
            'briefcase': Briefcase,
            'building': Building,
            'key': Key,
            'shield': Shield,
            'user-check': UserCheck,
            'user-plus': UserPlus,
            'user-x': UserX
        };

        const iconFunc = iconMap[iconName];
        if (iconFunc && typeof iconFunc === 'function') {
            const svg = iconFunc({});
            if (svg) {
                // Copy attributes
                Array.from(el.attributes).forEach(attr => {
                    if (attr.name !== 'data-lucide' && attr.name !== 'class') {
                        svg.setAttribute(attr.name, attr.value);
                    }
                });

                // Copy classes
                if (el.className) {
                    svg.className = el.className;
                }

                // Replace element
                el.parentNode.replaceChild(svg, el);
            }
        }
    });
}

// Initialize Echo with fallback
function initializeEcho() {
    try {
        // Check if required environment variables exist
        const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

        if (!pusherKey || pusherKey === 'null' || pusherKey === 'undefined') {
            console.warn('Pusher key not found. Real-time features disabled.');
            return createFallbackEcho();
        }

        const config = {
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
            forceTLS: false,
            wsHost: window.location.hostname,
            wsPort: 6001,
            wssPort: 6001,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            },
            authEndpoint: '/broadcasting/auth',
        };

        console.log('Initializing Echo with config:', {
            key: pusherKey ? '***' : 'NOT FOUND',
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
            wsHost: window.location.hostname,
            wsPort: 6001
        });

        const echoInstance = new Echo(config);
        window.Echo = echoInstance;

        // Connection events
        echoInstance.connector.pusher.connection.bind('connected', function() {
            console.log('Pusher connected');
        });

        echoInstance.connector.pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        console.log('Echo initialized successfully');
        return echoInstance;

    } catch (error) {
        console.warn('Echo initialization failed:', error);
        return createFallbackEcho();
    }
}

// Create fallback Echo object
function createFallbackEcho() {
    console.log('Using fallback Echo object');
    window.Echo = {
        join: () => ({
            here: () => console.log('Fallback: Users here'),
            joining: () => console.log('Fallback: User joining'),
            leaving: () => console.log('Fallback: User leaving'),
            listen: () => ({ stop: () => {} })
        }),
        private: (channel) => ({
            notification: (callback) => {
                console.log('Fallback: Notification listener added for', channel);
                return { stop: () => {} };
            },
            listen: (event, callback) => {
                console.log('Fallback: Event listener added for', event, 'on', channel);
                return { stop: () => {} };
            }
        }),
        connector: {
            pusher: {
                connection: {
                    bind: (event, callback) => {
                        console.log('Fallback: Connection event bound', event);
                    }
                }
            }
        }
    };
    return window.Echo;
}

// Get current user ID
function getCurrentUserId() {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    return userIdMeta ? parseInt(userIdMeta.getAttribute('content')) : null;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');

    // Initialize Lucide icons
    initializeLucideIcons();

    // Initialize Echo
    initializeEcho();
});

// Livewire event listeners
document.addEventListener('livewire:init', function() {
    console.log('Livewire initialized');

    // Re-initialize Lucide icons
    setTimeout(initializeLucideIcons, 100);

    // Setup Echo listeners if user is authenticated
    const userId = getCurrentUserId();
    if (userId && window.Echo) {
        setupEchoListeners(userId);
    }
});

document.addEventListener('livewire:navigated', function() {
    console.log('Livewire navigated');

    // Re-initialize icons after navigation
    setTimeout(initializeLucideIcons, 100);
});

// Setup Echo listeners
function setupEchoListeners(userId) {
    if (!window.Echo || !window.Echo.connector || !window.Echo.connector.pusher) {
        console.warn('Echo not properly initialized, skipping listener setup');
        return;
    }

    try {
        console.log('Setting up Echo listeners for user:', userId);

        // Private channel for user notifications
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('Notification received:', notification);
                showNotification(notification.title || 'Notification', notification.message || '');
            });

        // Notifications channel
        window.Echo.private(`notifications.${userId}`)
            .listen('.notification.created', (data) => {
                console.log('Custom notification:', data);
                showNotification(data.title || 'Message', data.message || '');
            });

        // Online users presence channel
        window.Echo.join('online-users')
            .here((users) => {
                console.log('Online users:', users);
                if (window.Livewire) {
                    window.Livewire.dispatch('online-users-here', { users });
                }
            })
            .joining((user) => {
                console.log('User joined:', user);
                if (window.Livewire) {
                    window.Livewire.dispatch('user-joining-online', { user });
                }
            })
            .leaving((user) => {
                console.log('User left:', user);
                if (window.Livewire) {
                    window.Livewire.dispatch('user-leaving-online', { user });
                }
            })
            .listen('.user.online.status', (data) => {
                console.log('User status changed:', data);
                if (window.Livewire) {
                    window.Livewire.dispatch('user-online-status-changed', data);
                }
            });

        console.log('Echo listeners setup complete');

    } catch (error) {
        console.error('Error setting up Echo listeners:', error);
    }
}

// Simple notification function
function showNotification(title, message) {
    // Check if notification container exists
    let container = document.getElementById('notificationContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'fixed top-4 right-4 z-50 space-y-2 max-w-sm';
        document.body.appendChild(container);
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-0';
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <strong>${title}</strong>
            <span>${message}</span>
            <button class="ml-auto opacity-70 hover:opacity-100" onclick="this.parentElement.parentElement.remove()">
                ×
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Livewire event handlers
if (window.Livewire) {
    window.Livewire.on('show-success', (message) => {
        showNotification('Success', message);
    });

    window.Livewire.on('show-error', (message) => {
        showNotification('Error', message);
    });

    window.Livewire.on('show-info', (message) => {
        showNotification('Info', message);
    });
}

// Page visibility change
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && window.Livewire) {
        window.Livewire.dispatch('page-became-visible');
    }
});

// Export for module usage
export { initializeEcho, initializeLucideIcons };
