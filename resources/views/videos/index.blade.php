<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Videos') }}
            </h2>
            <a href="{{ route('videos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Upload Video
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($uploads->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($uploads as $upload)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                                @if($upload->thumbnail_path)
                                    <img src="{{ asset('storage/' . $upload->thumbnail_path) }}" alt="{{ $upload->title }}" class="object-cover">
                                @else
                                    <div class="flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $upload->title }}</h3>
                                @if($upload->description)
                                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($upload->description, 100) }}</p>
                                @endif

                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($upload->status === 'ready') bg-green-100 text-green-800
                                        @elseif($upload->status === 'processing') bg-yellow-100 text-yellow-800
                                        @elseif($upload->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($upload->status) }}
                                    </span>
                                    @if($upload->duration)
                                        <span class="text-xs text-gray-500">{{ gmdate('H:i:s', $upload->duration) }}</span>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-500 mb-3">
                                    <p>{{ number_format($upload->file_size / 1024 / 1024, 1) }} MB</p>
                                    <p>{{ $upload->created_at->diffForHumans() }}</p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('videos.show', $upload->uuid) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View
                                        </a>
                                        @if($upload->isProcessed())
                                            <a href="{{ route('videos.edit', $upload->uuid) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                Edit
                                            </a>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('videos.destroy', $upload->uuid) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this video?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $uploads->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No videos yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by uploading your first video.</p>
                        <div class="mt-6">
                            <a href="{{ route('videos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload Video
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
