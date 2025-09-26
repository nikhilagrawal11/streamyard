<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('stream:cleanup', function () {
    $this->info('Starting stream cleanup...');
    \App\Jobs\CleanupEndedStreams::dispatch();
    $this->info('Stream cleanup job dispatched.');
})->purpose('Clean up old stream data');

Artisan::command('stream:process-scheduled', function () {
    $this->info('Processing scheduled streams...');

    $dueSchedules = \App\Models\StreamSchedule::where('status', 'scheduled')
        ->where('auto_start', true)
        ->where('scheduled_at', '<=', now())
        ->get();

    foreach ($dueSchedules as $schedule) {
        \App\Jobs\ScheduleStreamBroadcast::dispatch($schedule);
        $this->info("Dispatched broadcast job for schedule: {$schedule->title}");
    }

    $this->info("Processed {$dueSchedules->count()} scheduled streams.");
})->purpose('Process due scheduled streams');
