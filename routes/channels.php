<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('stream.{streamUuid}', function ($user, $streamUuid) {
    // Allow access if user is a participant of the stream
    $stream = \App\Models\Stream::where('uuid', $streamUuid)->first();

    if (!$stream) {
        return false;
    }

    return $stream->participants()->where('user_id', $user->id)->exists() ||
        $stream->user_id === $user->id;
});

// Private user channels
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Stream studio channel (for participants only)
Broadcast::channel('studio.{streamUuid}', function ($user, $streamUuid) {
    $stream = \App\Models\Stream::where('uuid', $streamUuid)->first();

    if (!$stream) {
        return false;
    }

    $participant = $stream->participants()
        ->where('user_id', $user->id)
        ->where('status', 'joined')
        ->first();

    return $participant ? [
        'id' => $participant->id,
        'name' => $participant->participant_name,
        'role' => $participant->role,
    ] : false;
});

// WebRTC signaling channel
Broadcast::channel('webrtc.{streamUuid}', function ($user, $streamUuid) {
    $stream = \App\Models\Stream::where('uuid', $streamUuid)->first();

    if (!$stream) {
        return false;
    }

    return $stream->participants()
        ->where('user_id', $user->id)
        ->where('status', 'joined')
        ->exists();
});
