<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamEnded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Stream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new Channel('stream.' . $this->stream->uuid),
            new PrivateChannel('user.' . $this->stream->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'stream.ended';
    }

    public function broadcastWith()
    {
        return [
            'stream' => [
                'id' => $this->stream->id,
                'uuid' => $this->stream->uuid,
                'title' => $this->stream->title,
                'status' => $this->stream->status,
                'ended_at' => $this->stream->ended_at,
            ]
        ];
    }
}
