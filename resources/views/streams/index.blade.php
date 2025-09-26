<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Streams') }}
            </h2>
            <a href="{{ route('streams.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Stream
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($streams->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($streams as $stream)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $stream->title }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($stream->status === 'live') bg-red-100 text-red-800
                                        @elseif($stream->status === 'scheduled') bg-yellow-100 text-yellow-800
                                        @elseif($stream->status === 'ended') bg-gray-100 text-gray-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($stream->status) }}
                                    </span>
                                </div>

                                @if($stream->description)
                                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($stream->description, 100) }}</p>
                                @endif

                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $stream->participants->count() }} participants

                                    @if($stream->scheduled_at)
                                        <span class="ml-4">
                                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $stream->scheduled_at->format('M j, Y H:i') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('streams.show', $stream->uuid) }}"
                                           class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View
                                        </a>
                                        @if($stream->canJoin())
                                            <a href="{{ route('streams.studio', $stream->uuid) }}"
                                               class="text-green-600 hover:text-green-900 text-sm">
                                                Studio
                                            </a>
                                        @endif
                                        <a href="{{ route('streams.edit', $stream->uuid) }}"
                                           class="text-blue-600 hover:text-blue-900 text-sm">
                                            Edit
                                        </a>
                                    </div>

                                    @if(!$stream->isLive())
                                        <form method="POST" action="{{ route('streams.destroy', $stream->uuid) }}"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this stream?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $streams->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No streams yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first stream.</p>
                        <div class="mt-6">
                            <a href="{{ route('streams.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Stream
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
