<?php

namespace App\Events;

use App\Models\CameraSource;
use App\Models\Stream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CameraSwitched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;
    public $cameraSource;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Stream $stream, CameraSource $cameraSource)
    {
        $this->stream = $stream;
        $this->cameraSource = $cameraSource;
    }

    public function broadcastOn()
    {
        return new Channel('stream.' . $this->stream->uuid);
    }

    public function broadcastAs()
    {
        return 'camera.switched';
    }

    public function broadcastWith()
    {
        return [
            'camera_source' => [
                'id' => $this->cameraSource->id,
                'source_name' => $this->cameraSource->source_name,
                'source_type' => $this->cameraSource->source_type,
                'is_active' => $this->cameraSource->is_active,
                'settings' => $this->cameraSource->settings,
            ]
        ];
    }
}
