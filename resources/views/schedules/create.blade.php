<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule Stream') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('schedules.store') }}">
                        @csrf

                        <!-- Video Selection -->
                        <div class="mb-4">
                            <label for="video_upload_id" class="block text-sm font-medium text-gray-700">Select Video</label>
                            <select name="video_upload_id" id="video_upload_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Choose a video to schedule...</option>
                                @foreach($videos as $video)
                                    <option value="{{ $video->id }}" {{ old('video_upload_id', request('video')) == $video->uuid ? 'selected' : '' }}>
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

                        <!-- Scheduled Date & Time -->
                        <div class="mb-4">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Scheduled Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('scheduled_at', now()->addHour()->format('Y-m-d\TH:i')) }}" required>
                            @error('scheduled_at')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label for="duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                            <input type="number" name="duration" id="duration" min="1" max="1440" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('duration') }}" placeholder="Leave empty for video length">
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
                                    <input type="checkbox" name="auto_start" value="1" class="form-checkbox" {{ old('auto_start') ? 'checked' : '' }}>
                                    <span class="ml-2">Auto-start at scheduled time</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="allow_chat" value="1" class="form-checkbox" {{ old('allow_chat') ? 'checked' : '' }}>
                                    <span class="ml-2">Allow chat during broadcast</span>
                                </label>
                                <br>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="record_broadcast" value="1" class="form-checkbox" {{ old('record_broadcast') ? 'checked' : '' }}>
                                    <span class="ml-2">Record the broadcast</span>
                                </label>
                            </div>
                        </div>

                        <!-- Video Preview -->
                        <div id="video-preview" class="mb-6 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video Preview</label>
                            <div class="aspect-w-16 aspect-h-9 bg-black rounded-lg overflow-hidden">
                                <video id="preview-video" class="w-full h-full object-cover" controls>
                                    <source src="" type="video/mp4">
                                </video>
                            </div>
                            <div id="video-info" class="mt-2 text-sm text-gray-600"></div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('schedules.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Schedule Stream
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
                const videoSelect = document.getElementById('video_upload_id');
                const videoPreview = document.getElementById('video-preview');
                const previewVideo = document.getElementById('preview-video');
                const videoInfo = document.getElementById('video-info');
                const titleInput = document.getElementById('title');

                videoSelect.addEventListener('change', async function() {
                    const videoId = this.value;
                    if (!videoId) {
                        videoPreview.classList.add('hidden');
                        return;
                    }

                    try {
                        // Get video details
                        const response = await fetch(`/api/videos/${this.options[this.selectedIndex].text.split(' ')[0]}`);
                        const data = await response.json();

                        if (data.video) {
                            const video = data.video;

                            // Auto-fill title if empty
                            if (!titleInput.value) {
                                titleInput.value = `Scheduled: ${video.title}`;
                            }

                            // Show video preview
                            previewVideo.src = `/storage/videos/${video.filename}`;
                            videoPreview.classList.remove('hidden');

                            // Show video info
                            let info = `Duration: ${video.duration ? new Date(video.duration * 1000).toISOString().substr(11, 8) : 'Unknown'}`;
                            info += ` | Size: ${(video.file_size / 1024 / 1024).toFixed(1)} MB`;
                            if (video.metadata && video.metadata.resolution) {
                                info += ` | Resolution: ${video.metadata.resolution}`;
                            }
                            videoInfo.textContent = info;
                        }
                    } catch (error) {
                        console.error('Error loading video details:', error);
                    }
                });

                // Set minimum datetime to current time
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                document.getElementById('scheduled_at').min = now.toISOString().slice(0, 16);
            });
        </script>
    @endpush
</x-app-layout>
