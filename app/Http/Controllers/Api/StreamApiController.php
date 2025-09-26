<?php

namespace App\Http\Controllers\Api;

use App\Events\StreamEnded;
use App\Events\StreamStarted;
use App\Http\Controllers\Controller;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamApiController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->streams()->with(['participants']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $streams = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($streams);
    }

    public function show(Stream $stream)
    {
        $this->authorize('view', $stream);

        return response()->json([
            'stream' => $stream->load(['participants.user', 'cameraSources', 'user'])
        ]);
    }

    public function start(Stream $stream)
    {
        $this->authorize('update', $stream);

        if ($stream->isLive()) {
            return response()->json(['error' => 'Stream is already live'], 422);
        }

        $stream->update([
            'status' => 'live',
            'started_at' => now(),
            'rtmp_url' => "rtmp://localhost:1935/live/" . $stream->stream_key,
            'playback_url' => "http://localhost:8080/hls/" . $stream->stream_key . ".m3u8",
        ]);

        event(new StreamStarted($stream));

        return response()->json([
            'success' => true,
            'message' => 'Stream started successfully',
            'stream' => $stream->fresh()
        ]);
    }

    public function stop(Stream $stream)
    {
        $this->authorize('update', $stream);

        if (!$stream->isLive()) {
            return response()->json(['error' => 'Stream is not live'], 422);
        }

        $stream->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        event(new StreamEnded($stream));

        return response()->json([
            'success' => true,
            'message' => 'Stream ended successfully',
            'stream' => $stream->fresh()
        ]);
    }

    public function join(Stream $stream, Request $request)
    {
        if (!$stream->canJoin()) {
            return response()->json(['error' => 'Cannot join this stream'], 422);
        }

        $validator = Validator::make($request->all(), [
            'participant_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $participant = $stream->participants()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'status' => 'joined',
                'participant_name' => $request->participant_name ?? auth()->user()->name,
                'participant_email' => auth()->user()->email,
                'joined_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'participant' => $participant,
            'stream' => $stream->fresh(['participants'])
        ]);
    }

    public function leave(Stream $stream)
    {
        $participant = $stream->participants()->where('user_id', auth()->id())->first();

        if ($participant) {
            $participant->update([
                'status' => 'left',
                'left_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Left stream successfully'
        ]);
    }

    public function publicInfo(Stream $stream)
    {
        // Return public information about the stream
        return response()->json([
            'uuid' => $stream->uuid,
            'title' => $stream->title,
            'description' => $stream->description,
            'status' => $stream->status,
            'type' => $stream->type,
            'viewer_count' => $stream->viewer_count,
            'is_live' => $stream->isLive(),
            'started_at' => $stream->started_at,
            'host' => [
                'name' => $stream->user->name,
            ],
            'playback_url' => $stream->status === 'live' ? $stream->playback_url : null,
        ]);
    }

    public function status(Stream $stream)
    {
        return response()->json([
            'status' => $stream->status,
            'is_live' => $stream->isLive(),
            'viewer_count' => $stream->viewer_count,
            'participant_count' => $stream->participants()->where('status', 'joined')->count(),
        ]);
    }

    // WebRTC Signaling methods
    public function signal(Request $request, Stream $stream)
    {
        $this->authorize('view', $stream);

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:offer,answer,ice-candidate',
            'data' => 'required|array',
            'target_user_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Broadcast signaling data to other participants
        broadcast(new \App\Events\WebRTCSignal(
            $stream,
            auth()->id(),
            $request->type,
            $request->data,
            $request->target_user_id
        ))->toOthers();

        return response()->json(['success' => true]);
    }

    public function offer(Request $request, Stream $stream)
    {
        return $this->handleWebRTCSignal($request, $stream, 'offer');
    }

    public function answer(Request $request, Stream $stream)
    {
        return $this->handleWebRTCSignal($request, $stream, 'answer');
    }

    public function iceCandidate(Request $request, Stream $stream)
    {
        return $this->handleWebRTCSignal($request, $stream, 'ice-candidate');
    }

    private function handleWebRTCSignal(Request $request, Stream $stream, string $type)
    {
        $this->authorize('view', $stream);

        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'target_user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Broadcast to specific user
        broadcast(new \App\Events\WebRTCSignal(
            $stream,
            auth()->id(),
            $type,
            $request->data,
            $request->target_user_id
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
