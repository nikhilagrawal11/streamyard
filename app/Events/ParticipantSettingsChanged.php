<?php

namespace App\Events;

use App\Models\StreamParticipant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantSettingsChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $participant;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(StreamParticipant $participant)
    {
        $this->participant = $participant;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('stream.' . $this->participant->stream->uuid);
    }

    public function broadcastAs()
    {
        return 'participant.settings.changed';
    }

    public function broadcastWith()
    {
        return [
            'participant' => [
                'id' => $this->participant->id,
                'user_id' => $this->participant->user_id,
                'camera_enabled' => $this->participant->camera_enabled,
                'microphone_enabled' => $this->participant->microphone_enabled,
                'screen_sharing' => $this->participant->screen_sharing,
                'video_settings' => $this->participant->video_settings,
            ]
        ];
    }
}
