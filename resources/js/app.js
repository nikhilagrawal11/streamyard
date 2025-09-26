import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Initialize Echo for real-time features
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

// Global streaming utilities
window.StreamingUtils = {
    // WebRTC configuration
    rtcConfiguration: {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    },

    // Get user media with error handling
    async getUserMedia(constraints = { video: true, audio: true }) {
        try {
            return await navigator.mediaDevices.getUserMedia(constraints);
        } catch (error) {
            console.error('Error accessing user media:', error);
            throw new Error('Unable to access camera or microphone');
        }
    },

    // Format duration from seconds
    formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);

        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    },

    // Show toast notifications
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 text-white ${
            type === 'error' ? 'bg-red-500' :
                type === 'success' ? 'bg-green-500' :
                    type === 'warning' ? 'bg-yellow-500' :
                        'bg-blue-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
};

// Global error handler for unhandled promises
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    window.StreamingUtils.showToast('An unexpected error occurred', 'error');
});
