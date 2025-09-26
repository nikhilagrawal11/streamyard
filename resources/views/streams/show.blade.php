<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $stream->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Created by {{ $stream->user->name }} • {{ $stream->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($stream->status === 'live') bg-red-100 text-red-800
                    @elseif($stream->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @elseif($stream->status === 'ended') bg-gray-100 text-gray-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ ucfirst($stream->status) }}
                </span>
                @can('update', $stream)
                    <div class="flex space-x-2">
                        <a href="{{ route('streams.edit', $stream->uuid) }}"
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Edit
                        </a>
                        @if($stream->canJoin())
                            <a href="{{ route('streams.studio', $stream->uuid) }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Join Studio
                            </a>
                        @endif
                    </div>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Stream Player -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="aspect-w-16 aspect-h-9 bg-black">
                            @if($stream->status === 'live' && $stream->playback_url)
                                <video id="stream-player"
                                       class="w-full h-full object-cover"
                                       controls
                                       autoplay
                                       muted>
                                    <source src="{{ $stream->playback_url }}" type="application/x-mpegURL">
                                    <p class="text-white text-center mt-8">
                                        Your browser doesn't support HLS playback.
                                    </p>
                                </video>
                            @elseif($stream->status === 'ended' && $stream->recording_path)
                                <video id="stream-player"
                                       class="w-full h-full object-cover"
                                       controls>
                                    <source src="{{ asset('storage/' . $stream->recording_path) }}" type="video/mp4">
                                    <p class="text-white text-center mt-8">
                                        Your browser doesn't support video playback.
                                    </p>
                                </video>
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center text-white">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        @if($stream->status === 'scheduled')
                                            <h3 class="text-lg font-medium mb-2">Stream Scheduled</h3>
                                            <p class="text-gray-300">
                                                @if($stream->scheduled_at)
                                                    Starts {{ $stream->scheduled_at->format('M j, Y \a\t H:i') }}
                                                @else
                                                    Waiting to go live...
                                                @endif
                                            </p>
                                        @elseif($stream->status === 'ended')
                                            <h3 class="text-lg font-medium mb-2">Stream Ended</h3>
                                            <p class="text-gray-300">This stream has ended.</p>
                                        @else
                                            <h3 class="text-lg font-medium mb-2">Stream Not Available</h3>
                                            <p class="text-gray-300">Stream is not currently broadcasting.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($stream->status === 'live')
                            <div class="bg-red-600 px-4 py-2">
                                <div class="flex items-center justify-between text-white">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-white rounded-full animate-pulse mr-2"></div>
                                        <span class="text-sm font-medium">LIVE</span>
                                    </div>
                                    <div class="text-sm">
                                        <span id="viewer-count">{{ $stream->viewer_count }}</span>
                                        viewers
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Stream Description -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">About this Stream</h3>

                            @if($stream->description)
                                <p class="text-gray-700 whitespace-pre-line mb-4">{{ $stream->description }}</p>
                            @else
                                <p class="text-gray-500 italic mb-4">No description provided.</p>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stream Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $stream->type }}</dd>
                                </div>

                                @if($stream->scheduled_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Scheduled Time</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $stream->scheduled_at->format('M j, Y \a\t H:i') }}</dd>
                                    </div>
                                @endif

                                @if($stream->started_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Started</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $stream->started_at->format('M j, Y \a\t H:i') }}</dd>
                                    </div>
                                @endif

                                @if($stream->ended_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $stream->started_at ? $stream->ended_at->diffForHumans($stream->started_at, true) : 'Unknown' }}
                                        </dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Maximum Participants</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stream->max_participants }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stream Key</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">
                                        @can('update', $stream)
                                            <span class="blur-sm hover:blur-none transition-all cursor-pointer"
                                                  title="Hover to reveal">
                                                {{ $stream->stream_key }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">Hidden</span>
                                        @endcan
                                    </dd>
                                </div>
                            </div>

                            @if($stream->settings)
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Stream Settings</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @if($stream->settings['allow_chat'] ?? false)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                💬 Chat Enabled
                                            </span>
                                        @endif
                                        @if($stream->settings['record_stream'] ?? false)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                📹 Recording
                                            </span>
                                        @endif
                                        @if($stream->settings['auto_start'] ?? false)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                ⚡ Auto-Start
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Participants List -->
                    @if($stream->participants->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Participants ({{ $stream->participants->count() }})
                                </h3>
                                <div class="space-y-3">
                                    @foreach($stream->participants as $participant)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                    @if($participant->user && $participant->user->avatar)
                                                        <img src="{{ $participant->user->avatar }}"
                                                             alt="{{ $participant->participant_name }}"
                                                             class="w-full h-full rounded-full object-cover">
                                                    @else
                                                        <span class="text-gray-600 font-medium text-sm">
                                                            {{ substr($participant->participant_name, 0, 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $participant->participant_name }}</p>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="text-xs text-gray-500 capitalize">{{ $participant->role }}</span>
                                                        <span class="px-2 py-0.5 text-xs rounded-full
                                                            @if($participant->status === 'joined') bg-green-100 text-green-800
                                                            @elseif($participant->status === 'invited') bg-yellow-100 text-yellow-800
                                                            @elseif($participant->status === 'left') bg-gray-100 text-gray-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($participant->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-1 text-sm">
                                                @if($participant->status === 'joined')
                                                    @if($participant->camera_enabled)
                                                        <span class="text-green-500" title="Camera on">📹</span>
                                                    @else
                                                        <span class="text-red-500" title="Camera off">📹</span>
                                                    @endif
                                                    @if($participant->microphone_enabled)
                                                        <span class="text-green-500" title="Microphone on">🎤</span>
                                                    @else
                                                        <span class="text-red-500" title="Microphone off">🎤</span>
                                                    @endif
                                                    @if($participant->screen_sharing)
                                                        <span class="text-blue-500" title="Screen sharing">🖥️</span>
                                                    @endif
                                                @endif
                                                @if($participant->joined_at)
                                                    <span class="text-xs text-gray-500">
                                                        {{ $participant->joined_at->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Host Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Host</h3>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                    @if($stream->user->avatar)
                                        <img src="{{ $stream->user->avatar }}" alt="{{ $stream->user->name }}"
                                             class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-gray-600 font-medium">
                                            {{ substr($stream->user->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $stream->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $stream->user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stream Actions -->
                    @can('update', $stream)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                                <div class="space-y-3">
                                    @if($stream->canJoin())
                                        <a href="{{ route('streams.studio', $stream->uuid) }}"
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            Enter Studio
                                        </a>
                                    @endif

                                    @if($stream->status === 'scheduled')
                                        <button id="start-stream"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293L12 11M15 9h-3.586a1 1 0 01-.707-.293L9.414 7.293A1 1 0 018.707 7H7a1 1 0 00-1 1v3a1 1 0 001 1h3.586l1.707 1.707a1 1 0 00.707.293H15a1 1 0 001-1V9a1 1 0 00-1-1z"></path>
                                            </svg>
                                            Start Stream
                                        </button>
                                    @elseif($stream->status === 'live')
                                        <button id="stop-stream"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                            </svg>
                                            Stop Stream
                                        </button>
                                    @endif

                                    <a href="{{ route('streams.edit', $stream->uuid) }}"
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Stream
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- Stream Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Current Viewers</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $stream->viewer_count }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Total Participants</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $stream->participants->count() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Active Participants</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ $stream->participants->where('status', 'joined')->count() }}
                                    </dd>
                                </div>
                                @if($stream->started_at && $stream->ended_at)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Total Duration</dt>
                                        <dd class="text-sm font-medium text-gray-900">
                                            {{ $stream->ended_at->diffForHumans($stream->started_at, true) }}
                                        </dd>
                                    </div>
                                @elseif($stream->started_at)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">Live Duration</dt>
                                        <dd class="text-sm font-medium text-gray-900" id="live-duration">
                                            {{ $stream->started_at->diffForHumans(null, true) }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Share Stream -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Share Stream</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stream URL</label>
                                    <div class="flex">
                                        <input type="text" id="stream-url"
                                               value="{{ route('streams.watch', $stream->uuid) }}" readonly
                                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50">
                                        <button onclick="copyStreamUrl()"
                                                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="https://twitter.com/intent/tweet?text=Join%20my%20live%20stream:%20{{ urlencode($stream->title) }}&url={{ urlencode(route('streams.watch', $stream->uuid)) }}"
                                       target="_blank"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Twitter
                                    </a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('streams.watch', $stream->uuid)) }}"
                                       target="_blank"
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Facebook
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Copy stream URL to clipboard
            function copyStreamUrl()
            {
                const urlInput = document.getElementById('stream-url');
                urlInput.select();
                urlInput.setSelectionRange(0, 99999);

                try
                {
                    document.execCommand('copy');

                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.textContent = 'Stream URL copied to clipboard!';
                    document.body.appendChild(toast);

                    setTimeout(() =>
                    {
                        toast.remove();
                    }, 3000);
                } catch (err)
                {
                    console.error('Failed to copy URL:', err);
                }
            }

            // Real-time updates
            @can('update', $stream)
            // Stream control buttons
            const startButton = document.getElementById('start-stream');
            const stopButton = document.getElementById('stop-stream');

            if (startButton)
            {
                startButton.addEventListener('click', async function ()
                {
                    try
                    {
                        const response = await fetch(`/api/streams/{{ $stream->uuid }}/start`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            location.reload();
                        } else
                        {
                            alert('Failed to start stream: ' + data.message);
                        }
                    } catch (error)
                    {
                        console.error('Error starting stream:', error);
                        alert('Failed to start stream');
                    }
                });
            }

            if (stopButton)
            {
                stopButton.addEventListener('click', async function ()
                {
                    if (!confirm('Are you sure you want to stop the stream?'))
                    {
                        return;
                    }

                    try
                    {
                        const response = await fetch(`/api/streams/{{ $stream->uuid }}/stop`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            location.reload();
                        } else
                        {
                            alert('Failed to stop stream: ' + data.message);
                        }
                    } catch (error)
                    {
                        console.error('Error stopping stream:', error);
                        alert('Failed to stop stream');
                    }
                });
            }
            @endcan

            // Listen for real-time stream updates
            @if($stream->status === 'live')
            window.Echo.channel('stream.{{ $stream->uuid }}')
                .listen('StreamEnded', (e) =>
                {
                    location.reload();
                })
                .listen('ParticipantJoined', (e) =>
                {
                    // Update participant count or reload
                    location.reload();
                })
                .listen('ParticipantLeft', (e) =>
                {
                    // Update participant count
                    location.reload();
                });
            @endif

            // Update live duration every minute
            @if($stream->status === 'live' && $stream->started_at)
            const startTime = new Date('{{ $stream->started_at->toISOString() }}');

            function updateLiveDuration()
            {
                const now = new Date();
                const duration = Math.floor((now - startTime) / 1000);

                const hours = Math.floor(duration / 3600);
                const minutes = Math.floor((duration % 3600) / 60);
                const seconds = duration % 60;

                let durationText = '';
                if (hours > 0)
                {
                    durationText = `${hours}h ${minutes}m`;
                } else if (minutes > 0)
                {
                    durationText = `${minutes}m ${seconds}s`;
                } else
                {
                    durationText = `${seconds}s`;
                }

                const durationElement = document.getElementById('live-duration');
                if (durationElement)
                {
                    durationElement.textContent = durationText;
                }
            }

            // Update immediately and then every 30 seconds
            updateLiveDuration();
            setInterval(updateLiveDuration, 30000);
            @endif

            // Initialize video player for HLS streams
            @if($stream->status === 'live' && $stream->playback_url)
            const video = document.getElementById('stream-player');

            if (Hls.isSupported())
            {
                const hls = new Hls({
                    enableWorker: false,
                    lowLatencyMode: true,
                    backBufferLength: 90
                });

                hls.loadSource('{{ $stream->playback_url }}');
                hls.attachMedia(video);

                hls.on(Hls.Events.MANIFEST_PARSED, function ()
                {
                    video.play().catch(e =>
                    {
                        console.log('Autoplay prevented:', e);
                        // Show play button overlay
                        const playButton = document.createElement('button');
                        playButton.innerHTML = '▶️ Click to Play';
                        playButton.className = 'absolute inset-0 bg-black bg-opacity-50 text-white text-xl font-bold cursor-pointer hover:bg-opacity-70 transition-all';
                        playButton.addEventListener('click', () =>
                        {
                            video.play();
                            playButton.remove();
                        });
                        video.parentElement.appendChild(playButton);
                    });
                });

                hls.on(Hls.Events.ERROR, function (event, data)
                {
                    if (data.fatal)
                    {
                        switch (data.type)
                        {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.log('Fatal network error encountered, try to recover');
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.log('Fatal media error encountered, try to recover');
                                hls.recoverMediaError();
                                break;
                            default:
                                console.log('Fatal error, cannot recover');
                                hls.destroy();
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl'))
            {
                // Safari native HLS support
                video.src = '{{ $stream->playback_url }}';
                video.addEventListener('loadedmetadata', function ()
                {
                    video.play().catch(e => console.log('Autoplay prevented:', e));
                });
            } else
            {
                console.error('HLS is not supported in this browser');
                // Show error message to user
                const errorDiv = document.createElement('div');
                errorDiv.className = 'absolute inset-0 flex items-center justify-center bg-black text-white text-center p-4';
                errorDiv.innerHTML = '<div><h3 class="text-lg font-bold mb-2">Playback Not Supported</h3><p>Your browser does not support HLS video playback.</p></div>';
                video.parentElement.appendChild(errorDiv);
            }
            @endif

            // Viewer count updates
            @if($stream->status === 'live')
            // Simulate viewer count updates (in a real app, this would come from the server)
            let currentViewers = {{ $stream->viewer_count }};

            function updateViewerCount()
            {
                fetch(`/api/streams/{{ $stream->uuid }}/status`)
                    .then(response => response.json())
                    .then(data =>
                    {
                        if (data.viewer_count !== currentViewers)
                        {
                            currentViewers = data.viewer_count;
                            const viewerElements = document.querySelectorAll('#viewer-count');
                            viewerElements.forEach(el =>
                            {
                                el.textContent = currentViewers;
                            });
                        }
                    })
                    .catch(error => console.log('Error fetching viewer count:', error));
            }

            // Update viewer count every 30 seconds
            setInterval(updateViewerCount, 30000);
            @endif
        </script>

        <!-- Include HLS.js for live stream playback -->
        @if($stream->status === 'live' && $stream->playback_url)
            <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        @endif
    @endpush
</x-app-layout>
