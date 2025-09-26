<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadVideoRequest;
use App\Jobs\ProcessVideoUpload;
use App\Models\VideoUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoApiController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->videoUploads();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $uploads = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        return response()->json($uploads);
    }

    public function show(VideoUpload $upload)
    {
        $this->authorize('view', $upload);

        return response()->json([
            'video' => $upload
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully',
            'video' => $upload
        ], 201);
    }

    public function destroy(VideoUpload $upload)
    {
        $this->authorize('delete', $upload);

        // Delete the file
        Storage::disk('public')->delete('videos/' . $upload->filename);

        // Delete thumbnail if exists
        if ($upload->thumbnail_path) {
            Storage::disk('public')->delete($upload->thumbnail_path);
        }

        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully'
        ]);
    }

    public function play(VideoUpload $upload)
    {
        $this->authorize('view', $upload);

        if (!$upload->isProcessed()) {
            return response()->json(['error' => 'Video is not ready for playback'], 422);
        }

        return response()->json([
            'success' => true,
            'video_url' => $upload->url,
            'thumbnail' => $upload->thumbnail_path ? asset('storage/' . $upload->thumbnail_path) : null,
            'duration' => $upload->duration,
            'metadata' => $upload->metadata,
        ]);
    }
}
