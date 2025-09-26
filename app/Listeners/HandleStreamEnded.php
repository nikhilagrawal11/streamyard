<?php

namespace App\Listeners;

use App\Events\StreamEnded;
use App\Events\StreamStarted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleStreamEnded
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(StreamEnded $event)
    {
        $stream = $event->stream;

        // Log stream end
        Log::info('Stream ended', [
            'stream_id' => $stream->id,
            'stream_uuid' => $stream->uuid,
            'title' => $stream->title,
            'user_id' => $stream->user_id,
            'ended_at' => $stream->ended_at,
            'duration' => $stream->started_at ? $stream->ended_at->diffInMinutes($stream->started_at) : 0,
        ]);

        // Finalize stream analytics
        $this->finalizeStreamAnalytics($stream);

        // Update all active participants to left status
        $stream->participants()
            ->where('status', 'joined')
            ->update([
                'status' => 'left',
                'left_at' => now(),
            ]);

        // Stop recording if it was enabled
        if ($stream->settings['record_stream'] ?? false) {
            $this->stopRecording($stream);
        }

        // Generate stream summary
        $this->generateStreamSummary($stream);
    }

    private function finalizeStreamAnalytics($stream)
    {
        // Finalize analytics data
        $duration = $stream->started_at ? $stream->ended_at->diffInMinutes($stream->started_at) : 0;
        $participantCount = $stream->participants()->where('status', 'joined')->count();

        // Store final analytics
        Log::info('Stream analytics finalized', [
            'stream_id' => $stream->id,
            'duration_minutes' => $duration,
            'participant_count' => $participantCount,
            'viewer_count' => $stream->viewer_count,
        ]);
    }

    private function stopRecording($stream)
    {
        // Stop recording process
        // This would integrate with your streaming server to stop recording
        Log::info('Recording stopped for stream', ['stream_id' => $stream->id]);

        // Process and store the recording file
        $this->processRecording($stream);
    }

    private function processRecording($stream)
    {
        // Process the recorded video file
        // Generate thumbnails, convert formats, etc.
    }

    private function generateStreamSummary($stream)
    {
        // Generate and send stream summary to host
        $summary = [
            'title' => $stream->title,
            'duration' => $stream->started_at ? $stream->ended_at->diffForHumans($stream->started_at, true) : '0 minutes',
            'participants' => $stream->participants()->count(),
            'peak_viewers' => $stream->viewer_count,
        ];

        // Mail::to($stream->user->email)->send(new StreamSummary($stream, $summary));
    }
}
