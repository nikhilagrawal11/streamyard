<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stream;
use App\Models\StreamParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantApiController extends Controller
{
    public function store(Request $request, Stream $stream)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'role' => 'in:guest,host'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->authorize('update', $stream);

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
            'message' => 'Invitation sent successfully',
            'participant' => $participant
        ], 201);
    }

    public function updateSettings(Request $request, StreamParticipant $participant)
    {
        $validator = Validator::make($request->all(), [
            'camera_enabled' => 'boolean',
            'microphone_enabled' => 'boolean',
            'screen_sharing' => 'boolean',
            'video_settings' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user can update this participant's settings
        if ($participant->user_id !== auth()->id() && !$participant->stream->user_id === auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $participant->update([
            'camera_enabled' => $request->boolean('camera_enabled'),
            'microphone_enabled' => $request->boolean('microphone_enabled'),
            'screen_sharing' => $request->boolean('screen_sharing'),
            'video_settings' => $request->video_settings ?? $participant->video_settings,
        ]);

        // Broadcast settings change to stream
        broadcast(new \App\Events\ParticipantSettingsChanged($participant))->toOthers();

        return response()->json([
            'success' => true,
            'participant' => $participant->fresh()
        ]);
    }

    public function destroy(StreamParticipant $participant)
    {
        $this->authorize('update', $participant->stream);

        if ($participant->isHost()) {
            return response()->json(['error' => 'Cannot remove the host'], 422);
        }

        $participant->update([
            'status' => 'kicked',
            'left_at' => now(),
        ]);

        // Broadcast participant removal
        broadcast(new \App\Events\ParticipantLeft($participant))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully'
        ]);
    }
}
