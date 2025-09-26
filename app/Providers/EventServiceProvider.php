<?php

namespace App\Providers;

use App\Events\ParticipantJoined;
use App\Events\StreamEnded;
use App\Events\StreamStarted;
use App\Listeners\HandleParticipantJoined;
use App\Listeners\HandleStreamEnded;
use App\Listeners\HandleStreamStarted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        StreamStarted::class => [
            HandleStreamStarted::class,
        ],
        StreamEnded::class => [
            HandleStreamEnded::class,
        ],
        ParticipantJoined::class => [
            HandleParticipantJoined::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
