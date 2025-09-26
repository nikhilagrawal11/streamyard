<?php

namespace App\Events;

use App\Models\StreamParticipant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantLeft
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
        return 'participant.left';
    }

    public function broadcastWith()
    {
        return [
            'participant' => [
                'id' => $this->participant->id,
                'user_id' => $this->participant->user_id,
                'participant_name' => $this->participant->participant_name,
                'status' => $this->participant->status,
                'left_at' => $this->participant->left_at,
            ]
        ];
    }
}
