<?php

namespace App\Jobs;

use App\Events\StreamEnded;
use App\Models\Stream;
use App\Models\StreamSchedule;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StopScheduledBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stream;
    protected $streamSchedule;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Stream $stream, StreamSchedule $streamSchedule)
    {
        $this->stream = $stream;
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
            Log::info('Stopping scheduled broadcast', [
                'stream_id' => $this->stream->id,
                'schedule_id' => $this->streamSchedule->id
            ]);

            // Check if stream is still live
            if ($this->stream->status !== 'live') {
                Log::warning('Stream is not live, cannot stop broadcast', [
                    'stream_id' => $this->stream->id,
                    'current_status' => $this->stream->status
                ]);
                return;
            }

            // Stop the stream
            $this->stream->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);

            // Update schedule status
            $this->streamSchedule->update(['status' => 'completed']);

            // Trigger stream ended event
            event(new StreamEnded($this->stream));

            Log::info('Scheduled broadcast stopped successfully', [
                'stream_id' => $this->stream->id,
                'schedule_id' => $this->streamSchedule->id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to stop scheduled broadcast', [
                'stream_id' => $this->stream->id,
                'schedule_id' => $this->streamSchedule->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error('StopScheduledBroadcast job failed', [
            'stream_id' => $this->stream->id,
            'schedule_id' => $this->streamSchedule->id,
            'exception' => $exception->getMessage()
        ]);
    }
}
