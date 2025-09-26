<?php

namespace App\Jobs;

use App\Models\Stream;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyStreamParticipants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $stream;
    protected $message;
    protected $notificationType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Stream $stream, string $message, string $notificationType = 'info')
    {
        $this->stream = $stream;
        $this->message = $message;
        $this->notificationType = $notificationType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $participants = $this->stream->participants()
            ->where('status', 'joined')
            ->with('user')
            ->get();

        foreach ($participants as $participant) {
            if ($participant->user && $participant->user->email) {
                // Send email notification
                // Mail::to($participant->user->email)->send(new StreamNotification($this->stream, $this->message, $this->notificationType));

                Log::info('Stream notification sent', [
                    'stream_id' => $this->stream->id,
                    'participant_id' => $participant->id,
                    'message' => $this->message,
                    'type' => $this->notificationType,
                ]);
            }

            // Send real-time notification via WebSocket
            broadcast(new \App\Events\StreamNotification(
                $this->stream,
                $participant->user_id,
                $this->message,
                $this->notificationType
            ))->toOthers();
        }
    }
}
