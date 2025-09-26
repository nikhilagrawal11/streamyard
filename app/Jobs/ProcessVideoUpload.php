<?php

namespace App\Jobs;

use App\Models\VideoUpload;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $videoUpload;
    public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(VideoUpload $videoUpload)
    {
        $this->videoUpload = $videoUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Starting video processing', ['upload_id' => $this->videoUpload->id]);

            // Update status to processing
            $this->videoUpload->update(['status' => 'processing']);

            // Get video file path
            $videoPath = storage_path('app/public/videos/' . $this->videoUpload->filename);

            if (!file_exists($videoPath)) {
                throw new Exception('Video file not found: ' . $videoPath);
            }

            // Extract video metadata
            $metadata = $this->extractVideoMetadata($videoPath);

            // Generate thumbnail
            $thumbnailPath = $this->generateThumbnail($videoPath);

            // Update video record with processed information
            $this->videoUpload->update([
                'status' => 'ready',
                'duration' => $metadata['duration'] ?? null,
                'thumbnail_path' => $thumbnailPath,
                'metadata' => $metadata,
            ]);

            Log::info('Video processing completed', ['upload_id' => $this->videoUpload->id]);

        } catch (Exception $e) {
            Log::error('Video processing failed', [
                'upload_id' => $this->videoUpload->id,
                'error' => $e->getMessage()
            ]);

            $this->videoUpload->update(['status' => 'failed']);

            throw $e;
        }
    }

    private function extractVideoMetadata($videoPath)
    {
        // This is a simplified version - in production you'd use FFmpeg
        // For now, we'll return basic metadata
        $filesize = filesize($videoPath);

        return [
            'filesize' => $filesize,
            'duration' => 0, // Would be extracted using FFmpeg
            'resolution' => '1920x1080', // Would be extracted using FFmpeg
            'fps' => 30, // Would be extracted using FFmpeg
            'codec' => 'h264', // Would be extracted using FFmpeg
            'processed_at' => now()->toISOString(),
        ];
    }

    private function generateThumbnail($videoPath)
    {
        // This is a simplified version - in production you'd use FFmpeg
        // For now, we'll create a placeholder thumbnail
        $thumbnailFilename = pathinfo($this->videoUpload->filename, PATHINFO_FILENAME) . '_thumb.jpg';
        $thumbnailPath = 'thumbnails/' . $thumbnailFilename;

        // Create a simple placeholder thumbnail
        // In production, you would use FFmpeg to extract a frame:
        // ffmpeg -i input.mp4 -ss 00:00:01 -vframes 1 -q:v 2 output.jpg

        $placeholderContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        Storage::disk('public')->put($thumbnailPath, $placeholderContent);

        return $thumbnailPath;
    }

    public function failed(Exception $exception)
    {
        Log::error('ProcessVideoUpload job failed', [
            'upload_id' => $this->videoUpload->id,
            'exception' => $exception->getMessage()
        ]);

        $this->videoUpload->update(['status' => 'failed']);
    }
}
