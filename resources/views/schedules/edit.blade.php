<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Schedule') }} - {{ $schedule->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('schedules.update', $schedule->uuid) }}">
                        @csrf
                        @method('PUT')

                        <!-- Video Selection -->
                        <div class="mb-4">
                            <label for="video_upload_id" class="block text-sm font-medium text-gray-700">Select Video</label>
                            <select name="video_upload_id" id="video_upload_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Choose a video to schedule...</option>
                                @foreach($videos as $video)
                                    <option value="{{ $video->id }}" {{ old('video_upload_id', $schedule->video_upload_id) == $video->id ? 'selected' : '' }}>
                                        {{ $video->title }} ({{ number_format($video->file_size / 1024 / 1024, 1) }} MB)
                                    </option>
                                @endforeach
                            </select>
                            @error('video_upload_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Schedule Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('title', $schedule->title) }}" required>
                            @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $schedule->description) }}</textarea>
                            @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Scheduled Date & Time -->
                        <div class="mb-4">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Scheduled Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('scheduled_at', $schedule->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                            @error('scheduled_at')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label for="duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                            <input type="number" name="duration" id="duration" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('duration', $schedule->duration) }}" placeholder="Leave empty for video length">
                            <p class="mt-1 text-sm text-gray-500">Maximum 24 hours (1440 minutes). Leave empty to use full video duration.</p>
                            @error('duration')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Settings -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Settings</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="auto_start" value="1" class="form-checkbox" {{ old('auto_start', $schedule->auto_start) ? 'checked' : '' }}>
                                    <span class="ml-2">Auto-start at scheduled time</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="allow_chat" value="1" class="form-checkbox" {{ old('allow_chat', $schedule->settings['allow_chat'] ?? false) ? 'checked' : '' }}>
                                    <span class="ml-2">Allow chat during broadcast</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="record_broadcast" value="1" class="form-checkbox" {{ old('record_broadcast', $schedule->settings['record_broadcast'] ?? false) ? 'checked' : '' }}>
                                    <span class="ml-2">Record the broadcast</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('schedules.show', $schedule->uuid) }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Set minimum datetime to current time
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                document.getElementById('scheduled_at').min = now.toISOString().slice(0, 16);
            });
        </script>
    @endpush
</x-app-layout>
