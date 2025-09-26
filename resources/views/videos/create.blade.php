<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Video') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data"
                          id="video-upload-form">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Video Title</label>
                            <input type="text" name="title" id="title"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   value="{{ old('title') }}" required>
                            @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Video Upload -->
                        <div class="mb-6">
                            <label for="video" class="block text-sm font-medium text-gray-700">Video File</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                         viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="video"
                                               class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a video</span>
                                            <input id="video" name="video" type="file" class="sr-only" accept="video/*"
                                                   required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">MP4, AVI, MOV, WMV, FLV, WebM, MKV up to 1GB</p>
                                </div>
                            </div>
                            @error('video')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload-progress" class="mb-4 hidden">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div id="progress-bar"
                                     class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                     style="width: 0%"></div>
                            </div>
                            <p id="progress-text" class="text-sm text-gray-600 mt-2">Uploading... 0%</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('videos.index') }}"
                               class="mr-4 text-sm text-gray-600 hover:text-gray-900">Cancel
                            </a>
                            <button type="submit" id="upload-button"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function ()
            {
                const form = document.getElementById('video-upload-form');
                const videoInput = document.getElementById('video');
                const uploadButton = document.getElementById('upload-button');
                const progressContainer = document.getElementById('upload-progress');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

                // Handle file selection
                videoInput.addEventListener('change', function (e)
                {
                    const file = e.target.files[0];
                    if (file)
                    {
                        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                        console.log(`Selected file: ${file.name} (${fileSize} MB)`);
                    }
                });

                // Handle form submission with progress
                form.addEventListener('submit', function (e)
                {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const xhr = new XMLHttpRequest();

                    // Show progress
                    progressContainer.classList.remove('hidden');
                    uploadButton.disabled = true;
                    uploadButton.textContent = 'Uploading...';

                    // Track upload progress
                    xhr.upload.addEventListener('progress', function (e)
                    {
                        if (e.lengthComputable)
                        {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
                        }
                    });

                    // Handle completion
                    xhr.addEventListener('load', function ()
                    {
                        if (xhr.status === 302 || xhr.status === 200)
                        {
                            // Redirect on success
                            window.location.href = "{{ route('videos.index') }}";
                        } else
                        {
                            // Handle error
                            progressText.textContent = 'Upload failed. Please try again.';
                            progressBar.classList.add('bg-red-600');
                            uploadButton.disabled = false;
                            uploadButton.textContent = 'Upload Video';
                        }
                    });

                    // Handle error
                    xhr.addEventListener('error', function ()
                    {
                        progressText.textContent = 'Upload failed. Please try again.';
                        progressBar.classList.add('bg-red-600');
                        uploadButton.disabled = false;
                        uploadButton.textContent = 'Upload Video';
                    });

                    // Send the request
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    xhr.send(formData);
                });

                // Drag and drop functionality
                const dropZone = form.querySelector('.border-dashed');

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName =>
                {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e)
                {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName =>
                {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName =>
                {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e)
                {
                    dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
                }

                function unhighlight(e)
                {
                    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
                }

                dropZone.addEventListener('drop', handleDrop, false);

                function handleDrop(e)
                {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    if (files.length > 0)
                    {
                        videoInput.files = files;

                        // Trigger change event
                        const event = new Event('change', {bubbles: true});
                        videoInput.dispatchEvent(event);
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
