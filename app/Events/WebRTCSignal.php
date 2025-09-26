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

class WebRTCSignal
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;
    public $fromUserId;
    public $signalType;
    public $signalData;
    public $targetUserId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Stream $stream, $fromUserId, $signalType, $signalData, $targetUserId = null)
    {
        $this->stream = $stream;
        $this->fromUserId = $fromUserId;
        $this->signalType = $signalType;
        $this->signalData = $signalData;
        $this->targetUserId = $targetUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->targetUserId) {
            return new PrivateChannel('user.' . $this->targetUserId);
        }

        return new Channel('webrtc.' . $this->stream->uuid);
    }

    public function broadcastAs()
    {
        return 'webrtc.signal';
    }

    public function broadcastWith()
    {
        return [
            'stream_uuid' => $this->stream->uuid,
            'from_user_id' => $this->fromUserId,
            'signal_type' => $this->signalType,
            'signal_data' => $this->signalData,
            'target_user_id' => $this->targetUserId,
        ];
    }
}
