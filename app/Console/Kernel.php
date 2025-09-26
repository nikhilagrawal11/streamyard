<?php

namespace App\Console;

use App\Jobs\CleanupEndedStreams;
use App\Jobs\ScheduleStreamBroadcast;
use App\Models\StreamSchedule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CleanupEndedStreams)->dailyAt('02:00');

        // Check for scheduled streams every minute
        $schedule->call(function () {
            $dueSchedules = StreamSchedule::where('status', 'scheduled')
                ->where('auto_start', true)
                ->where('scheduled_at', '<=', now())
                ->get();

            foreach ($dueSchedules as $schedule) {
                ScheduleStreamBroadcast::dispatch($schedule);
            }
        })->everyMinute();

        // Queue worker health check
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
