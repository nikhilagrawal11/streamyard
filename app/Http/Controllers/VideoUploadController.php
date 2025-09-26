<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadVideoRequest;
use App\Jobs\ProcessVideoUpload;
use App\Models\VideoUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadController extends Controller
{
    public function index()
    {
        $uploads = auth()->user()->videoUploads()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('videos.index', compact('uploads'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(UploadVideoRequest $request)
    {
        $file = $request->file('video');
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

        // Store the video file
        $path = $file->storeAs('videos', $filename, 'public');

        $upload = auth()->user()->videoUploads()->create([
            'title' => $request->title,
            'description' => $request->description,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'processing',
        ]);

        // Queue video processing
        ProcessVideoUpload::dispatch($upload);

        return redirect()->route('videos.show', $upload->uuid)
            ->with('success', 'Video uploaded successfully! Processing...');
    }

    public function show(VideoUpload $upload)
    {
        $this->authorize('view', $upload);

        return view('videos.show', compact('upload'));
    }

    public function edit(VideoUpload $upload)
    {
        $this->authorize('update', $upload);

        return view('videos.edit', compact('upload'));
    }

    public function update(UploadVideoRequest $request, VideoUpload $upload)
    {
        $this->authorize('update', $upload);

        $upload->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('videos.show', $upload->uuid)
            ->with('success', 'Video updated successfully!');
    }

    public function destroy(VideoUpload $upload)
    {
        $this->authorize('delete', $upload);

        // Delete the file
        Storage::disk('public')->delete('videos/' . $upload->filename);

        // Delete thumbnail if exists
        if ($upload->thumbnail_path)
        {
            Storage::disk('public')->delete($upload->thumbnail_path);
        }

        $upload->delete();

        return redirect()->route('videos.index')
            ->with('success', 'Video deleted successfully!');
    }

    public function play(VideoUpload $upload, Request $request)
    {
        $this->authorize('view', $upload);

        if (!$upload->isProcessed())
        {
            return response()->json(['error' => 'Video is not ready for playback'], 422);
        }

        return response()->json([
            'success' => true,
            'video_url' => $upload->url,
            'thumbnail' => $upload->thumbnail_path ? asset('storage/' . $upload->thumbnail_path) : null,
            'duration' => $upload->duration,
        ]);
    }
}
