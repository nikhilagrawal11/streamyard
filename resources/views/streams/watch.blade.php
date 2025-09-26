<x-guest-layout>
    <div class="min-h-screen bg-gray-900">
        <!-- Stream Player -->
        <div class="relative">
            <div class="aspect-w-16 aspect-h-9 bg-black">
                @if($stream->status === 'live' && $stream->playback_url)
                    <video id="public-stream-player"
                           class="w-full h-full object-cover"
                           controls
                           autoplay
                           muted>
                        <source src="{{ $stream->playback_url }}" type="application/x-mpegURL">
                        <p class="text-white text-center mt-8">
                            Your browser doesn't support HLS playback.
                        </p>
                    </video>
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-white">
                            <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            @if($stream->status === 'scheduled')
                                <h2 class="text-2xl font-bold mb-2">Stream Starting Soon</h2>
                                @if($stream->scheduled_at)
                                    <p class="text-gray-300 mb-4">
                                        Scheduled for {{ $stream->scheduled_at->format('M j, Y \a\t H:i') }}
                                    </p>
                                @endif
                            @elseif($stream->status === 'ended')
                                <h2 class="text-2xl font-bold mb-2">Stream Has Ended</h2>
                                <p class="text-gray-300 mb-4">Thanks for watching!</p>
                            @else
                                <h2 class="text-2xl font-bold mb-2">Stream Offline</h2>
                                <p class="text-gray-300 mb-4">This stream is not currently broadcasting.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            @if($stream->status === 'live')
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-6">
                    <div class="flex items-end justify-between text-white">
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-bold truncate mb-1">{{ $stream->title }}</h1>
                            <p class="text-gray-300 text-sm">{{ $stream->user->name }}</p>
                        </div>
                        <div class="flex items-center space-x-4 ml-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></div>
                                <span class="text-red-500 font-medium">LIVE</span>
                            </div>
                            <div class="text-sm">
                                <span id="public-viewer-count">{{ $stream->viewer_count }}</span>
                                viewers
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Stream Info -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-white mb-4">{{ $stream->title }}</h2>
                        @if($stream->description)
                            <p class="text-gray-300 whitespace-pre-line">{{ $stream->description }}</p>
                        @endif
                    </div>

                    <!-- Chat for live streams -->
                    @if($stream->status === 'live' && ($stream->settings['allow_chat'] ?? false))
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-white mb-4">Live Chat</h3>
                            <div id="public-chat-messages" class="h-64 overflow-y-auto mb-4 space-y-2">
                                <!-- Chat messages will appear here -->
                            </div>
                            <div class="flex">
                                <input type="text" id="public-chat-input" placeholder="Join the conversation..."
                                       class="flex-1 bg-gray-700 text-white rounded-l px-3 py-2 text-sm border border-gray-600 focus:outline-none focus:border-indigo-500">
                                <button id="public-send-message"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-r transition-colors">
                                    Send
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Host Info -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Hosted by</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-medium">
                                    {{ substr($stream->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $stream->user->name }}</p>
                                <p class="text-gray-400 text-sm">Host</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stream Stats -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Stream Info</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Status</span>
                                <span class="text-white capitalize">{{ $stream->status }}</span>
                            </div>
                            @if($stream->status === 'live')
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Viewers</span>
                                    <span class="text-white"
                                          id="sidebar-viewer-count">{{ $stream->viewer_count }}</span>
                                </div>
                            @endif
                            @if($stream->started_at)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Started</span>
                                    <span class="text-white">{{ $stream->started_at->format('H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Join Stream -->
                    @auth
                        @if($stream->canJoin())
                            <div class="bg-gray-800 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-white mb-4">Join Stream</h3>
                                <p class="text-gray-300 text-sm mb-4">Want to participate in this stream?</p>
                                <a href="{{ route('streams.show', $stream->uuid) }}"
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Join as Participant
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-white mb-4">Join Stream</h3>
                            <p class="text-gray-300 text-sm mb-4">Sign up to participate in streams.</p>
                            <a href="{{ route('register') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Sign Up
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Include HLS.js for live stream playback -->
        @if($stream->status === 'live' && $stream->playback_url)
            <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
            <script>
                const publicVideo = document.getElementById('public-stream-player');

                if (Hls.isSupported())
                {
                    const hls = new Hls();
                    hls.loadSource('{{ $stream->playback_url }}');
                    hls.attachMedia(publicVideo);

                    hls.on(Hls.Events.MANIFEST_PARSED, function ()
                    {
                        publicVideo.play().catch(e => console.log('Autoplay prevented:', e));
                    });
                } else if (publicVideo.canPlayType('application/vnd.apple.mpegurl'))
                {
                    publicVideo.src = '{{ $stream->playback_url }}';
                    publicVideo.addEventListener('loadedmetadata', function ()
                    {
                        publicVideo.play().catch(e => console.log('Autoplay prevented:', e));
                    });
                }
            </script>
        @endif

        <!-- Public chat functionality -->
        @if($stream->status === 'live' && ($stream->settings['allow_chat'] ?? false))
            <script>
                // Public chat (simplified version)
                const chatInput = document.getElementById('public-chat-input');
                const sendButton = document.getElementById('public-send-message');
                const messagesContainer = document.getElementById('public-chat-messages');

                function addPublicChatMessage(data)
                {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'text-sm';

                    const time = new Date(data.timestamp).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                    messageDiv.innerHTML = `
                    <div class="text-gray-400 text-xs">${time}</div>
                    <div><span class="text-indigo-400 font-medium">${data.user}:</span> <span class="text-white">${data.message}</span></div>
                `;

                    messagesContainer.appendChild(messageDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }

                @auth
                function sendPublicChatMessage()
                {
                    const message = chatInput.value.trim();
                    if (!message) return;

                    addPublicChatMessage({
                        user: '{{ auth()->user()->name }}',
                        message: message,
                        timestamp: new Date()
                    });

                    // In a real implementation, this would broadcast to all viewers
                    chatInput.value = '';
                }

                sendButton.addEventListener('click', sendPublicChatMessage);
                chatInput.addEventListener('keypress', (e) =>
                {
                    if (e.key === 'Enter') sendPublicChatMessage();
                });
                @else
                    chatInput.placeholder = 'Sign in to chat...';
                chatInput.disabled = true;
                sendButton.disabled = true;
                @endauth
            </script>
        @endif
    @endpush
</x-guest-layout>
