<?php

namespace App\Policies;

use App\Models\StreamSchedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StreamSchedulePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, StreamSchedule $streamSchedule)
    {
        return $user->id === $streamSchedule->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, StreamSchedule $streamSchedule)
    {
        return $user->id === $streamSchedule->user_id && $streamSchedule->isScheduled();
    }

    public function delete(User $user, StreamSchedule $streamSchedule)
    {
        return $user->id === $streamSchedule->user_id && $streamSchedule->status !== 'broadcasting';
    }

    public function restore(User $user, StreamSchedule $streamSchedule)
    {
        return $user->id === $streamSchedule->user_id;
    }

    public function forceDelete(User $user, StreamSchedule $streamSchedule)
    {
        return $user->id === $streamSchedule->user_id;
    }
}
