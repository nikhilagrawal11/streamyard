<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $schedule->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Scheduled for {{ $schedule->scheduled_at->format('M j, Y \a\t H:i') }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($schedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @elseif($schedule->status === 'broadcasting') bg-red-100 text-red-800
                    @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($schedule->status) }}
                </span>
                @can('update', $schedule)
                    <div class="flex space-x-2">
                        @if($schedule->status === 'scheduled')
                            <a href="{{ route('schedules.edit', $schedule->uuid) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Edit
                            </a>
                            @if($schedule->scheduled_at <= now())
                                <button id="broadcast-now" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Go Live Now
                                </button>
                            @endif
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
                    <!-- Video Preview -->
                    @if($schedule->videoUpload)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="aspect-w-16 aspect-h-9 bg-black">
                                @if($schedule->videoUpload->isProcessed())
                                    <video id="schedule-video" class="w-full h-full object-cover" controls>
                                        <source src="{{ $schedule->videoUpload->url }}" type="{{ $schedule->videoUpload->mime_type }}">
                                        <p class="text-white text-center mt-8">
                                            Your browser doesn't support video playback.
                                        </p>
                                    </video>
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <div class="text-center text-white">
                                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium mb-2">Video Processing</h3>
                                            <p class="text-gray-300">Video is still being processed...</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($schedule->status === 'broadcasting')
                                <div class="bg-red-600 px-4 py-2">
                                    <div class="flex items-center justify-between text-white">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse mr-2"></div>
                                            <span class="text-sm font-medium">BROADCASTING</span>
                                        </div>
                                        <div class="text-sm">
                                            Started {{ $schedule->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Schedule Description -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">About this Schedule</h3>

                            @if($schedule->description)
                                <p class="text-gray-700 whitespace-pre-line mb-4">{{ $schedule->description }}</p>
                            @else
                                <p class="text-gray-500 italic mb-4">No description provided.</p>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $schedule->status }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Scheduled Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $schedule->scheduled_at->format('M j, Y \a\t H:i') }}</dd>
                                </div>

                                @if($schedule->duration)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $schedule->duration }} minutes</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Auto Start</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $schedule->auto_start ? 'Enabled' : 'Disabled' }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $schedule->created_at->format('M j, Y \a\t H:i') }}</dd>
                                </div>

                                @if($schedule->videoUpload)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Video File</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $schedule->videoUpload->title }}</dd>
                                    </div>
                                @endif
                            </div>

                            @if($schedule->settings)
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Broadcast Settings</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @if($schedule->settings['allow_chat'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                💬 Chat Enabled
                                            </span>
                                        @endif
                                        @if($schedule->settings['record_broadcast'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                📹 Recording
                                            </span>
                                        @endif
                                        @if($schedule->auto_start)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                ⚡ Auto-Start
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Countdown -->
                    @if($schedule->status === 'scheduled')
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    @if($schedule->scheduled_at > now())
                                        Time Until Broadcast
                                    @else
                                        Ready to Broadcast
                                    @endif
                                </h3>
                                <div id="countdown" class="text-center">
                                    @if($schedule->scheduled_at > now())
                                        <div class="grid grid-cols-4 gap-1 text-center">
                                            <div>
                                                <div class="text-2xl font-bold text-indigo-600" id="days">0</div>
                                                <div class="text-xs text-gray-500">DAYS</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-indigo-600" id="hours">0</div>
                                                <div class="text-xs text-gray-500">HOURS</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-indigo-600" id="minutes">0</div>
                                                <div class="text-xs text-gray-500">MINUTES</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-indigo-600" id="seconds">0</div>
                                                <div class="text-xs text-gray-500">SECONDS</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-green-600">
                                            <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="font-medium">Ready to Go Live!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Video Information -->
                    @if($schedule->videoUpload)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Video Information</h3>
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($schedule->videoUpload->thumbnail_path)
                                            <img src="{{ asset('storage/' . $schedule->videoUpload->thumbnail_path) }}" alt="{{ $schedule->videoUpload->title }}" class="h-16 w-16 rounded object-cover">
                                        @else
                                            <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $schedule->videoUpload->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ number_format($schedule->videoUpload->file_size / 1024 / 1024, 1) }} MB</p>
                                        @if($schedule->videoUpload->duration)
                                            <p class="text-sm text-gray-500">{{ gmdate('H:i:s', $schedule->videoUpload->duration) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('videos.show', $schedule->videoUpload->uuid) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    View Video Details →
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    @can('update', $schedule)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                                <div class="space-y-3">
                                    @if($schedule->status === 'scheduled')
                                        @if($schedule->scheduled_at <= now())
                                            <button onclick="broadcastNow()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293L12 11M15 9h-3.586a1 1 0 01-.707-.293L9.414 7.293A1 1 0 018.707 7H7a1 1 0 00-1 1v3a1 1 0 001 1h3.586l1.707 1.707a1 1 0 00.707.293H15a1 1 0 001-1V9a1 1 0 00-1-1z"></path>
                                                </svg>
                                                Start Broadcasting Now
                                            </button>
                                        @endif

                                        <a href="{{ route('schedules.edit', $schedule->uuid) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit Schedule
                                        </a>

                                        <form method="POST" action="{{ route('schedules.destroy', $schedule->uuid) }}" class="w-full" onsubmit="return confirm('Are you sure you want to cancel this schedule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancel Schedule
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            @if($schedule->status === 'scheduled')
            // Countdown timer
            const scheduledTime = new Date('{{ $schedule->scheduled_at->toISOString() }}');

            function updateCountdown() {
                const now = new Date();
                const difference = scheduledTime.getTime() - now.getTime();

                if (difference > 0) {
                    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                    document.getElementById('days').textContent = days.toString().padStart(2, '0');
                    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
                } else {
                    // Time has passed, reload page to show "Ready" state
                    location.reload();
                }
            }

            // Update countdown every second
            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
            @endif

            async function broadcastNow() {
                if (!confirm('Are you sure you want to start broadcasting this schedule now?')) {
                    return;
                }

                try {
                    const response = await fetch(`/api/schedules/{{ $schedule->uuid }}/broadcast`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to start broadcast: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error starting broadcast:', error);
                    alert('Failed to start broadcast');
                }
            }

            // Auto-refresh if video is still processing
            @if($schedule->videoUpload && $schedule->videoUpload->status === 'processing')
            setTimeout(() => {
                location.reload();
            }, 15000); // Refresh every 15 seconds
            @endif
        </script>
    @endpush
</x-app-layout>
