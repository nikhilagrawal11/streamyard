<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Stream') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('streams.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Stream Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('title') }}" required>
                            @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stream Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Stream Type</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="live" class="form-radio" {{ old('type', 'live') === 'live' ? 'checked' : '' }}>
                                    <span class="ml-2">Live Stream</span>
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="radio" name="type" value="pre_recorded" class="form-radio" {{ old('type') === 'pre_recorded' ? 'checked' : '' }}>
                                    <span class="ml-2">Pre-recorded Stream</span>
                                </label>
                            </div>
                            @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scheduled At -->
                        <div class="mb-4">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule For (Optional)</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('scheduled_at') }}">
                            @error('scheduled_at')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Participants -->
                        <div class="mb-4">
                            <label for="max_participants" class="block text-sm font-medium text-gray-700">Maximum Participants</label>
                            <input type="number" name="max_participants" id="max_participants" min="2" max="50" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('max_participants', 10) }}">
                            @error('max_participants')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Settings -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stream Settings</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="allow_chat" value="1" class="form-checkbox" {{ old('allow_chat') ? 'checked' : '' }}>
                                    <span class="ml-2">Allow chat during stream</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="record_stream" value="1" class="form-checkbox" {{ old('record_stream') ? 'checked' : '' }}>
                                    <span class="ml-2">Record stream</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="auto_start" value="1" class="form-checkbox" {{ old('auto_start') ? 'checked' : '' }}>
                                    <span class="ml-2">Auto-start at scheduled time</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('streams.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Create Stream
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
