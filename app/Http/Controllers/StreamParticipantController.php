<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\StreamParticipant;
use Illuminate\Http\Request;

class StreamParticipantController extends Controller
{
    public function invite(Request $request, Stream $stream)
    {
        $this->authorize('update', $stream);

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'role' => 'in:guest,host'
        ]);

        $participant = $stream->participants()->create([
            'user_id' => null, // For guest invitations
            'role' => $request->role ?? 'guest',
            'status' => 'invited',
            'participant_name' => $request->name,
            'participant_email' => $request->email,
        ]);

        // Send invitation email here
        // Mail::to($request->email)->send(new StreamInvitation($stream, $participant));

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully!',
            'participant' => $participant
        ]);
    }

    public function kick(StreamParticipant $participant)
    {
        $this->authorize('update', $participant->stream);

        if ($participant->isHost()) {
            return response()->json(['error' => 'Cannot kick the host!'], 422);
        }

        $participant->update([
            'status' => 'kicked',
            'left_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully!'
        ]);
    }

    public function updateSettings(Request $request, StreamParticipant $participant)
    {
        $request->validate([
            'camera_enabled' => 'boolean',
            'microphone_enabled' => 'boolean',
            'screen_sharing' => 'boolean',
        ]);

        $participant->update([
            'camera_enabled' => $request->boolean('camera_enabled'),
            'microphone_enabled' => $request->boolean('microphone_enabled'),
            'screen_sharing' => $request->boolean('screen_sharing'),
        ]);

        return response()->json([
            'success' => true,
            'participant' => $participant->fresh()
        ]);
    }
}
