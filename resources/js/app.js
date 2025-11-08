import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.StreamingUtils = {
    rtcConfiguration: {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    },

    async getUserMedia(constraints = { video: true, audio: true }) {
        try {
            return await navigator.mediaDevices.getUserMedia(constraints);
        } catch (error) {
            console.error('Error accessing user media:', error);
            throw new Error('Unable to access camera or microphone');
        }
    },

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
    },

    chat: {
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        createMessageElement(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex items-start space-x-2 mb-3';
            messageDiv.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                    <span class="text-white text-xs font-medium">${message.username.charAt(0).toUpperCase()}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-white">${this.escapeHtml(message.username)}</span>
                        <span class="text-xs text-gray-400">${this.formatTime(message.sent_at)}</span>
                    </div>
                    <p class="text-sm text-gray-200 mt-1">${this.escapeHtml(message.message)}</p>
                </div>
            `;
            return messageDiv;
        }
    }
};

window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    window.StreamingUtils.showToast('An unexpected error occurred', 'error');
});
