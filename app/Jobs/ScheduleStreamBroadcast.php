<?php

namespace App\Jobs;

use App\Events\StreamStarted;
use App\Models\Stream;
use App\Models\StreamSchedule;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScheduleStreamBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StreamSchedule $streamSchedule)
    {
        $this->streamSchedule = $streamSchedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Starting scheduled broadcast', ['schedule_id' => $this->streamSchedule->id]);

            // Check if schedule is still valid
            if ($this->streamSchedule->status !== 'scheduled') {
                Log::warning('Schedule no longer in scheduled status', [
                    'schedule_id' => $this->streamSchedule->id,
                    'current_status' => $this->streamSchedule->status
                ]);
                return;
            }

            // Check if video is ready
            if (!$this->streamSchedule->videoUpload || !$this->streamSchedule->videoUpload->isProcessed()) {
                Log::error('Video not ready for scheduled broadcast', [
                    'schedule_id' => $this->streamSchedule->id,
                    'video_id' => $this->streamSchedule->video_upload_id
                ]);

                $this->streamSchedule->update(['status' => 'failed']);
                return;
            }

            // Update schedule status
            $this->streamSchedule->update(['status' => 'broadcasting']);

            // Create stream for the scheduled broadcast
            $stream = $this->createStreamFromSchedule();

            // Start the broadcast
            $this->startBroadcast($stream);

            Log::info('Scheduled broadcast started successfully', [
                'schedule_id' => $this->streamSchedule->id,
                'stream_id' => $stream->id
            ]);

            // Schedule the end of broadcast if duration is set
            if ($this->streamSchedule->duration) {
                StopScheduledBroadcast::dispatch($stream, $this->streamSchedule)
                    ->delay(now()->addMinutes($this->streamSchedule->duration));
            }

        } catch (Exception $e) {
            Log::error('Scheduled broadcast failed', [
                'schedule_id' => $this->streamSchedule->id,
                'error' => $e->getMessage()
            ]);

            $this->streamSchedule->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function createStreamFromSchedule()
    {
        return $this->streamSchedule->user->streams()->create([
            'title' => $this->streamSchedule->title,
            'description' => $this->streamSchedule->description,
            'type' => 'pre_recorded',
            'status' => 'live',
            'started_at' => now(),
            'recording_path' => $this->streamSchedule->videoUpload->file_path,
            'settings' => array_merge($this->streamSchedule->settings ?? [], [
                'is_scheduled' => true,
                'schedule_id' => $this->streamSchedule->id,
            ]),
        ]);
    }

    private function startBroadcast(Stream $stream)
    {
        // This would integrate with your streaming server to start broadcasting the video
        // For now, we'll just update the stream URLs
        $stream->update([
            'rtmp_url' => "rtmp://localhost:1935/playback/" . $stream->stream_key,
            'playback_url' => "http://localhost:8080/hls/" . $stream->stream_key . ".m3u8",
        ]);

        // Trigger stream started event
        event(new StreamStarted($stream));
    }

    public function failed(Exception $exception)
    {
        Log::error('ScheduleStreamBroadcast job failed', [
            'schedule_id' => $this->streamSchedule->id,
            'exception' => $exception->getMessage()
        ]);

        $this->streamSchedule->update(['status' => 'failed']);
    }
}
