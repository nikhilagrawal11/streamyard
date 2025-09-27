<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('stream.' . $this->chatMessage->stream->uuid);
    }

    public function broadcastAs()
    {
        return 'chat.message';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->chatMessage->id,
                'uuid' => $this->chatMessage->uuid,
                'username' => $this->chatMessage->username,
                'message' => $this->chatMessage->message,
                'type' => $this->chatMessage->type,
                'sent_at' => $this->chatMessage->sent_at->toISOString(),
                'user' => $this->chatMessage->user ? [
                    'id' => $this->chatMessage->user->id,
                    'name' => $this->chatMessage->user->name,
                ] : null,
            ]
        ];
    }
}
