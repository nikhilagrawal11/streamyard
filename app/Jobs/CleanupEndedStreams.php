<?php

namespace App\Jobs;

use App\Models\Stream;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupEndedStreams implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Clean up streams that ended more than 30 days ago
        $cutoffDate = Carbon::now()->subDays(30);

        $streamsToCleanup = Stream::where('status', 'ended')
            ->where('ended_at', '<', $cutoffDate)
            ->get();

        foreach ($streamsToCleanup as $stream) {
            $this->cleanupStream($stream);
        }

        Log::info('Cleanup completed', [
            'streams_processed' => $streamsToCleanup->count(),
            'cutoff_date' => $cutoffDate,
        ]);
    }

    private function cleanupStream(Stream $stream)
    {
        try {
            // Clean up temporary files
            if ($stream->recording_path && Storage::disk('public')->exists($stream->recording_path)) {
                // Archive or delete recording based on settings
                if ($stream->settings['auto_delete_recordings'] ?? false) {
                    Storage::disk('public')->delete($stream->recording_path);
                    Log::info('Stream recording deleted', ['stream_id' => $stream->id]);
                }
            }

            // Clean up thumbnails
            if ($stream->thumbnail_path && Storage::disk('public')->exists($stream->thumbnail_path)) {
                Storage::disk('public')->delete($stream->thumbnail_path);
            }

            // Update stream status
            $stream->update([
                'recording_path' => null,
                'thumbnail_path' => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup stream', [
                'stream_id' => $stream->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
