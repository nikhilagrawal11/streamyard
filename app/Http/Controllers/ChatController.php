<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\Stream;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request, Stream $stream)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'type' => 'sometimes|in:message,emoji'
        ]);

        // Check if user can send messages in this stream
        if (!$this->canSendMessage($stream)) {
            return response()->json(['error' => 'Not authorized to send messages'], 403);
        }

        $chatMessage = $stream->chatMessages()->create([
            'user_id' => auth()->id(),
            'username' => auth()->user()->name,
            'message' => $request->message,
            'type' => $request->type ?? 'message',
            'metadata' => $request->metadata,
        ]);

        // Broadcast the message
        broadcast(new ChatMessageSent($chatMessage))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $chatMessage->load('user')
        ]);
    }

    public function index(Stream $stream)
    {
        // Get recent chat messages
        $messages = $stream->chatMessages()
            ->with('user')
            ->orderBy('sent_at', 'desc')
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages
        ]);
    }

    private function canSendMessage(Stream $stream)
    {
        // Check if user is stream owner
        if ($stream->user_id === auth()->id()) {
            return true;
        }

        // Check if user is a participant
        $participant = $stream->participants()->where('user_id', auth()->id())->first();
        if ($participant && $participant->status === 'joined') {
            return true;
        }

        // Check if chat is enabled for viewers (if implemented)
        if ($stream->settings['allow_public_chat'] ?? false) {
            return true;
        }

        return false;
    }
}
