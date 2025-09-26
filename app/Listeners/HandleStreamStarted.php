<?php

namespace App\Listeners;

use App\Events\StreamStarted;
use Illuminate\Support\Facades\Log;

class HandleStreamStarted
{
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
     * @param  object  $event
     * @return void
     */
    public function handle(StreamStarted $event)
    {
        $stream = $event->stream;

        // Log stream start
        Log::info('Stream started', [
            'stream_id' => $stream->id,
            'stream_uuid' => $stream->uuid,
            'title' => $stream->title,
            'user_id' => $stream->user_id,
            'started_at' => $stream->started_at,
        ]);

        // Initialize stream analytics
        $this->initializeStreamAnalytics($stream);

        // Send notifications to participants
        $this->notifyParticipants($stream);

        // Start recording if enabled
        if ($stream->settings['record_stream'] ?? false) {
            $this->startRecording($stream);
        }
    }

    private function initializeStreamAnalytics($stream)
    {
        // Initialize analytics tracking
        // This could integrate with services like Google Analytics, Mixpanel, etc.
    }

    private function notifyParticipants($stream)
    {
        // Send notifications to invited participants
        $participants = $stream->participants()->where('status', 'invited')->get();

        foreach ($participants as $participant) {
            // Send email notification
            // Mail::to($participant->participant_email)->send(new StreamStartedNotification($stream, $participant));
        }
    }

    private function startRecording($stream)
    {
        // Start recording process
        // This would integrate with your streaming server to start recording
        Log::info('Recording started for stream', ['stream_id' => $stream->id]);
    }
}
