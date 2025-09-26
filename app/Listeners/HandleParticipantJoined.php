<?php

namespace App\Listeners;

use App\Events\ParticipantJoined;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleParticipantJoined
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
     * @param  object  $event
     * @return void
     */
    public function handle(ParticipantJoined $event)
    {
        $participant = $event->participant;
        $stream = $participant->stream;

        // Log participant join
        Log::info('Participant joined stream', [
            'stream_id' => $stream->id,
            'participant_id' => $participant->id,
            'participant_name' => $participant->participant_name,
            'role' => $participant->role,
        ]);

        // Update stream participant count
        $this->updateStreamParticipantCount($stream);

        // Send welcome message or setup participant
        $this->setupParticipant($participant);
    }

    private function updateStreamParticipantCount($stream)
    {
        $activeCount = $stream->participants()->where('status', 'joined')->count();

        // Update viewer count if needed
        if ($activeCount > $stream->viewer_count) {
            $stream->update(['viewer_count' => $activeCount]);
        }
    }

    private function setupParticipant($participant)
    {
        // Setup participant's initial settings
        // Initialize their video/audio settings
        // Send them stream information
    }
}
