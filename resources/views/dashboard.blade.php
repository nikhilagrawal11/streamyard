<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500">Live Streams</h2>
                                <p class="text-lg font-semibold text-gray-700">{{ $liveStreams->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v11h6V6H9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500">Total Streams</h2>
                                <p class="text-lg font-semibold text-gray-700">{{ $totalStreams }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v11h6V6H9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500">Video Uploads</h2>
                                <p class="text-lg font-semibold text-gray-700">{{ $totalUploads }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-sm font-medium text-gray-500">Watch Time</h2>
                                <p class="text-lg font-semibold text-gray-700">{{ $totalWatchTime }} min</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('streams.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 w-full justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Stream
                            </a>
                            <a href="{{ route('videos.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 w-full justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload Video
                            </a>
                            <a href="{{ route('schedules.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150 w-full justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 110 2h-1v9a2 2 0 01-2 2H8a2 2 0 01-2-2V9H5a1 1 0 110-2h3z"></path>
                                </svg>
                                Schedule Stream
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Live Streams</h3>
                        @if($liveStreams->count() > 0)
                            <div class="space-y-3">
                                @foreach($liveStreams as $stream)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $stream->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $stream->participants->count() }}
                                                participants
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('streams.studio', $stream->uuid) }}"
                                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                Join Studio
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No live streams currently.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Schedules</h3>
                        @if($upcomingSchedules->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingSchedules as $schedule)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $schedule->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $schedule->scheduled_at->format('M j, Y H:i') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('schedules.show', $schedule->uuid) }}"
                                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No upcoming schedules.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Video Uploads</h3>
                    @if($recentUploads->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($recentUploads as $upload)
                                <div class="border rounded-lg p-4">
                                    <div class="aspect-w-16 aspect-h-9 mb-3">
                                        @if($upload->thumbnail_path)
                                            <img src="{{ asset('storage/' . $upload->thumbnail_path) }}"
                                                 alt="{{ $upload->title }}" class="object-cover rounded">
                                        @else
                                            <div class="bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-1">{{ $upload->title }}</h4>
                                    <p class="text-xs text-gray-500 mb-2">{{ $upload->created_at->diffForHumans() }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($upload->status === 'ready') bg-green-100 text-green-800
                                            @elseif($upload->status === 'processing') bg-yellow-100 text-yellow-800
                                            @elseif($upload->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($upload->status) }}
                                        </span>
                                        <a href="{{ route('videos.show', $upload->uuid) }}"
                                           class="text-indigo-600 hover:text-indigo-900 text-xs">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No recent uploads.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
