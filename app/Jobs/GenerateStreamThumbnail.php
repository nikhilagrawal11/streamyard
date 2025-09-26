<?php

namespace App\Jobs;

use App\Models\Stream;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateStreamThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stream;

    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Generate thumbnail from stream or recording
            if ($this->stream->recording_path && Storage::disk('public')->exists($this->stream->recording_path)) {
                $thumbnailPath = $this->generateThumbnailFromVideo($this->stream->recording_path);

                $this->stream->update([
                    'thumbnail_path' => $thumbnailPath
                ]);

                Log::info('Stream thumbnail generated', [
                    'stream_id' => $this->stream->id,
                    'thumbnail_path' => $thumbnailPath,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to generate stream thumbnail', [
                'stream_id' => $this->stream->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateThumbnailFromVideo($videoPath)
    {
        // This would use FFmpeg in production
        // ffmpeg -i input.mp4 -ss 00:00:01 -vframes 1 -q:v 2 thumbnail.jpg

        $thumbnailFilename = 'stream_' . $this->stream->id . '_thumb.jpg';
        $thumbnailPath = 'thumbnails/' . $thumbnailFilename;

        // Create a placeholder thumbnail for now
        $placeholderContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        Storage::disk('public')->put($thumbnailPath, $placeholderContent);

        return $thumbnailPath;
    }
}
