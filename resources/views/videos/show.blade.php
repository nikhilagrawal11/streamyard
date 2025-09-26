<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $upload->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Uploaded {{ $upload->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($upload->status === 'ready') bg-green-100 text-green-800
                    @elseif($upload->status === 'processing') bg-yellow-100 text-yellow-800
                    @elseif($upload->status === 'failed') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($upload->status) }}
                </span>
                @can('update', $upload)
                    <a href="{{ route('videos.edit', $upload->uuid) }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Video Player -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="aspect-w-16 aspect-h-9 bg-black">
                            @if($upload->isProcessed())
                                <video id="video-player" class="w-full h-full object-cover" controls>
                                    <source src="{{ $upload->url }}" type="{{ $upload->mime_type }}">
                                    <p class="text-white text-center mt-8">
                                        Your browser doesn't support video playback.
                                    </p>
                                </video>
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center text-white">
                                        @if($upload->status === 'processing')
                                            <svg class="mx-auto h-12 w-12 mb-4 animate-spin" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium mb-2">Processing Video</h3>
                                            <p class="text-gray-300">Please wait while we process your video...</p>
                                        @elseif($upload->status === 'failed')
                                            <svg class="mx-auto h-12 w-12 mb-4 text-red-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium mb-2 text-red-400">Processing Failed</h3>
                                            <p class="text-gray-300">There was an error processing your video.</p>
                                        @else
                                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium mb-2">Video Not Ready</h3>
                                            <p class="text-gray-300">Video is not available for playback.</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Video Description -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                            @if($upload->description)
                                <p class="text-gray-700 whitespace-pre-line">{{ $upload->description }}</p>
                            @else
                                <p class="text-gray-500 italic">No description provided.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Video Info Sidebar -->
                <div class="space-y-6">
                    <!-- Video Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Video Details</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $upload->status }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">File Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($upload->file_size / 1024 / 1024, 1) }}
                                        MB
                                    </dd>
                                </div>

                                @if($upload->duration)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ gmdate('H:i:s', $upload->duration) }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Format</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ strtoupper(pathinfo($upload->filename, PATHINFO_EXTENSION)) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Original Filename</dt>
                                    <dd class="mt-1 text-sm text-gray-900 break-all">{{ $upload->original_filename }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $upload->created_at->format('M j, Y \a\t H:i') }}</dd>
                                </div>

                                @if($upload->metadata)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Resolution</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $upload->metadata['resolution'] ?? 'Unknown' }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Frame Rate</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $upload->metadata['fps'] ?? 'Unknown' }}
                                            fps
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Actions -->
                    @can('update', $upload)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                                <div class="space-y-3">
                                    <a href="{{ route('videos.edit', $upload->uuid) }}"
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Video
                                    </a>

                                    @if($upload->isProcessed())
                                        <a href="{{ route('schedules.create', ['video' => $upload->uuid]) }}"
                                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Schedule Stream
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('videos.destroy', $upload->uuid) }}"
                                          class="w-full"
                                          onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Video
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- Usage -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Usage</h3>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">This video can be used in:</p>
                                <ul class="text-sm text-gray-500 space-y-1 ml-4">
                                    <li>• Live stream playback</li>
                                    <li>• Scheduled broadcasts</li>
                                    <li>• Stream overlays</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @if($upload->isProcessed())
            <script>
                // Auto-refresh if video is still processing
                @if($upload->status === 'processing')
                setTimeout(() =>
                {
                    location.reload();
                }, 10000); // Refresh every 10 seconds
                @endif
            </script>
        @endif
    @endpush
</x-app-layout>
