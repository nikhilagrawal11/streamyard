<?php

namespace App\Http\Controllers;

use App\Events\StreamEnded;
use App\Events\StreamStarted;
use App\Http\Requests\CreateStreamRequest;
use App\Models\Stream;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function index()
    {
        $streams = auth()->user()->streams()
            ->with(['participants'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('streams.index', compact('streams'));
    }

    public function create()
    {
        return view('streams.create');
    }

    public function store(CreateStreamRequest $request)
    {
        $stream = auth()->user()->streams()->create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'scheduled_at' => $request->scheduled_at,
            'max_participants' => $request->max_participants ?? 10,
            'settings' => [
                'allow_chat' => $request->boolean('allow_chat'),
                'record_stream' => $request->boolean('record_stream'),
                'auto_start' => $request->boolean('auto_start'),
            ]
        ]);

        // Create host participant
        $stream->participants()->create([
            'user_id' => auth()->id(),
            'role' => 'host',
            'status' => 'joined',
            'participant_name' => auth()->user()->name,
            'participant_email' => auth()->user()->email,
        ]);

        return redirect()->route('streams.show', $stream->uuid)
            ->with('success', 'Stream created successfully!');
    }

    public function show(Stream $stream)
    {
        $this->authorize('view', $stream);

        $stream->load(['participants.user', 'cameraSources', 'user']);

        return view('streams.show', compact('stream'));
    }

    public function edit(Stream $stream)
    {
        $this->authorize('update', $stream);

        return view('streams.edit', compact('stream'));
    }

    public function update(CreateStreamRequest $request, Stream $stream)
    {
        $this->authorize('update', $stream);

        $stream->update([
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => $request->scheduled_at,
            'max_participants' => $request->max_participants ?? 10,
            'settings' => array_merge($stream->settings ?? [], [
                'allow_chat' => $request->boolean('allow_chat'),
                'record_stream' => $request->boolean('record_stream'),
            ])
        ]);

        return redirect()->route('streams.show', $stream->uuid)
            ->with('success', 'Stream updated successfully!');
    }

    public function destroy(Stream $stream)
    {
        $this->authorize('delete', $stream);

        if ($stream->isLive())
        {
            return back()->with('error', 'Cannot delete a live stream!');
        }

        $stream->delete();

        return redirect()->route('streams.index')
            ->with('success', 'Stream deleted successfully!');
    }

    public function start(Stream $stream)
    {
        $this->authorize('update', $stream);

        if ($stream->isLive())
        {
            return back()->with('error', 'Stream is already live!');
        }

        $stream->update([
            'status' => 'live',
            'started_at' => now(),
            'rtmp_url' => $this->generateRtmpUrl($stream),
            'playback_url' => $this->generatePlaybackUrl($stream),
        ]);

        event(new StreamStarted($stream));

        return response()->json([
            'success' => true,
            'message' => 'Stream started successfully!',
            'stream' => $stream->fresh()
        ]);
    }

    public function stop(Stream $stream)
    {
        $this->authorize('update', $stream);

        if (!$stream->isLive())
        {
            return back()->with('error', 'Stream is not live!');
        }

        $stream->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        event(new StreamEnded($stream));

        return response()->json([
            'success' => true,
            'message' => 'Stream ended successfully!',
            'stream' => $stream->fresh()
        ]);
    }

    public function join(Stream $stream, Request $request)
    {
        if (!$stream->canJoin())
        {
            return back()->with('error', 'Cannot join this stream!');
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
            'redirect_url' => route('streams.studio', $stream->uuid)
        ]);
    }

    public function leave(Stream $stream)
    {
        $participant = $stream->participants()->where('user_id', auth()->id())->first();

        if ($participant)
        {
            $participant->update([
                'status' => 'left',
                'left_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Left stream successfully!'
        ]);
    }

    public function studio(Stream $stream)
    {
        $this->authorize('view', $stream);

        $participant = $stream->participants()->where('user_id', auth()->id())->first();

        if (!$participant || !$participant->isActive())
        {
            return redirect()->route('streams.show', $stream->uuid)
                ->with('error', 'You must join the stream first!');
        }

        $stream->load(['participants' => function ($query)
        {
            $query->where('status', 'joined')->with('user');
        }, 'cameraSources']);

        return view('streams.studio', compact('stream', 'participant'));
    }

    private function generateRtmpUrl(Stream $stream)
    {
        return "rtmp://localhost:1935/live/" . $stream->stream_key;
    }

    private function generatePlaybackUrl(Stream $stream)
    {
        return "http://localhost:8080/hls/" . $stream->stream_key . ".m3u8";
    }
}
