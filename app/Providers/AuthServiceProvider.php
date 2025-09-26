<?php

namespace App\Providers;

use App\Models\Stream;
use App\Models\StreamSchedule;
use App\Models\VideoUpload;
use App\Policies\StreamPolicy;
use App\Policies\StreamSchedulePolicy;
use App\Policies\VideoUploadPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Stream::class => StreamPolicy::class,
        VideoUpload::class => VideoUploadPolicy::class,
        StreamSchedule::class => StreamSchedulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Additional gates can be defined here
        Gate::define('manage-stream', function ($user, $stream)
        {
            return $user->id === $stream->user_id;
        });

        Gate::define('join-stream', function ($user, $stream)
        {
            if (!$stream->canJoin())
            {
                return false;
            }

            // Check if user is already a participant
            return $stream->participants()->where('user_id', $user->id)->exists() ||
                $stream->user_id === $user->id;
        });

        Gate::define('moderate-stream', function ($user, $stream)
        {
            $participant = $stream->participants()->where('user_id', $user->id)->first();

            return $participant && ($participant->isHost() || $stream->user_id === $user->id);
        });
    }
}
