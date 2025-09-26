<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stream Schedules') }}
            </h2>
            <a href="{{ route('schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Schedule Stream
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($schedules->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @foreach($schedules as $schedule)
                            <li>
                                <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($schedule->videoUpload && $schedule->videoUpload->thumbnail_path)
                                                    <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $schedule->videoUpload->thumbnail_path) }}" alt="{{ $schedule->title }}">
                                                @else
                                                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4 min-w-0 flex-1">
                                                <div class="flex items-center">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $schedule->title }}</p>
                                                    <span class="ml-2 px-2 py-1 text-xs rounded-full font-medium
                                                        @if($schedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                        @elseif($schedule->status === 'broadcasting') bg-red-100 text-red-800
                                                        @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($schedule->status) }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <p class="flex-shrink-0 text-xs text-gray-500">
                                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $schedule->scheduled_at->format('M j, Y \a\t H:i') }}
                                                        @if($schedule->duration)
                                                            • {{ $schedule->duration }} min
                                                        @endif
                                                    </p>
                                                </div>
                                                @if($schedule->videoUpload)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Video: {{ $schedule->videoUpload->title }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($schedule->status === 'scheduled' && $schedule->scheduled_at <= now())
                                                <button onclick="broadcastSchedule('{{ $schedule->uuid }}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    Go Live Now
                                                </button>
                                            @endif

                                            <a href="{{ route('schedules.show', $schedule->uuid) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View</a>

                                            @if($schedule->status === 'scheduled')
                                                <a href="{{ route('schedules.edit', $schedule->uuid) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>

                                                <form method="POST" action="{{ route('schedules.destroy', $schedule->uuid) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this schedule?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    {{ $schedules->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No scheduled streams</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by scheduling your first stream.</p>
                        <div class="mt-6">
                            <a href="{{ route('schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Schedule Stream
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            async function broadcastSchedule(scheduleUuid) {
                if (!confirm('Are you sure you want to start broadcasting this schedule now?')) {
                    return;
                }

                try {
                    const response = await fetch(`/api/schedules/${scheduleUuid}/broadcast`, {
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
        </script>
    @endpush
</x-app-layout>
