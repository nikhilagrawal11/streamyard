<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantJoined
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
        return 'participant.joined';
    }

    public function broadcastWith()
    {
        return [
            'participant' => [
                'id' => $this->participant->id,
                'user_id' => $this->participant->user_id,
                'participant_name' => $this->participant->participant_name,
                'role' => $this->participant->role,
                'joined_at' => $this->participant->joined_at,
                'camera_enabled' => $this->participant->camera_enabled,
                'microphone_enabled' => $this->participant->microphone_enabled,
            ]
        ];
    }
}
