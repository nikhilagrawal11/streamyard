<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleStreamRequest;
use App\Jobs\ScheduleStreamBroadcast;
use App\Models\StreamSchedule;
use Illuminate\Http\Request;

class StreamScheduleController extends Controller
{
    public function index()
    {
        $schedules = auth()->user()->streamSchedules()
            ->with(['videoUpload'])
            ->orderBy('scheduled_at')
            ->paginate(10);

        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $videos = auth()->user()->videoUploads()
            ->where('status', 'ready')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('schedules.create', compact('videos'));
    }

    public function store(ScheduleStreamRequest $request)
    {
        $schedule = auth()->user()->streamSchedules()->create([
            'video_upload_id' => $request->video_upload_id,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => $request->scheduled_at,
            'duration' => $request->duration,
            'auto_start' => $request->boolean('auto_start'),
            'settings' => [
                'allow_chat' => $request->boolean('allow_chat'),
                'record_broadcast' => $request->boolean('record_broadcast'),
            ]
        ]);

        // Queue the scheduled broadcast
        if ($schedule->auto_start) {
            ScheduleStreamBroadcast::dispatch($schedule)->delay($schedule->scheduled_at);
        }

        return redirect()->route('schedules.show', $schedule->uuid)
            ->with('success', 'Stream scheduled successfully!');
    }

    public function show(StreamSchedule $schedule)
    {
        $this->authorize('view', $schedule);

        $schedule->load(['videoUpload', 'user']);

        return view('schedules.show', compact('schedule'));
    }

    public function edit(StreamSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $videos = auth()->user()->videoUploads()
            ->where('status', 'ready')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('schedules.edit', compact('schedule', 'videos'));
    }

    public function update(ScheduleStreamRequest $request, StreamSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        if ($schedule->status !== 'scheduled') {
            return back()->with('error', 'Cannot update a schedule that is not in scheduled status!');
        }

        $schedule->update([
            'video_upload_id' => $request->video_upload_id,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => $request->scheduled_at,
            'duration' => $request->duration,
            'auto_start' => $request->boolean('auto_start'),
            'settings' => array_merge($schedule->settings ?? [], [
                'allow_chat' => $request->boolean('allow_chat'),
                'record_broadcast' => $request->boolean('record_broadcast'),
            ])
        ]);

        return redirect()->route('schedules.show', $schedule->uuid)
            ->with('success', 'Schedule updated successfully!');
    }

    public function destroy(StreamSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        if ($schedule->status === 'broadcasting') {
            return back()->with('error', 'Cannot delete an active broadcast!');
        }

        $schedule->update(['status' => 'cancelled']);

        return redirect()->route('schedules.index')
            ->with('success', 'Schedule cancelled successfully!');
    }

    public function broadcast(StreamSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        if ($schedule->status !== 'scheduled') {
            return response()->json(['error' => 'Schedule is not available for broadcasting'], 422);
        }

        if (!$schedule->videoUpload || !$schedule->videoUpload->isProcessed()) {
            return response()->json(['error' => 'Video is not ready for broadcasting'], 422);
        }

        $schedule->update(['status' => 'broadcasting']);

        // Create a stream for this scheduled broadcast
        $stream = $schedule->user->streams()->create([
            'title' => $schedule->title,
            'description' => $schedule->description,
            'type' => 'pre_recorded',
            'status' => 'live',
            'started_at' => now(),
            'recording_path' => $schedule->videoUpload->file_path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Broadcast started successfully!',
            'stream' => $stream,
            'schedule' => $schedule->fresh()
        ]);
    }
}
