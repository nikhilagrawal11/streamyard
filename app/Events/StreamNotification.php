<?php

namespace App\Events;

use App\Models\Stream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;
    public $userId;
    public $message;
    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Stream $stream, $userId, $message, $type = 'info')
    {
        $this->stream = $stream;
        $this->userId = $userId;
        $this->message = $message;
        $this->type = $type;
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
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'stream.notification';
    }

    public function broadcastWith()
    {
        return [
            'stream_uuid' => $this->stream->uuid,
            'message' => $this->message,
            'type' => $this->type,
            'timestamp' => now()->toISOString(),
        ];
    }
}
