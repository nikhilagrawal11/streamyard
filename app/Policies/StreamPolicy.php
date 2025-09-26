<?php

namespace App\Policies;

use App\Models\Stream;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StreamPolicy
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

    public function view(User $user, Stream $stream)
    {
        // Owner can always view
        if ($user->id === $stream->user_id) {
            return true;
        }

        // Participants can view
        return $stream->participants()->where('user_id', $user->id)->exists();
    }

    public function create(User $user)
    {
        return $user->canCreateStream();
    }

    public function update(User $user, Stream $stream)
    {
        return $user->id === $stream->user_id;
    }

    public function delete(User $user, Stream $stream)
    {
        return $user->id === $stream->user_id && !$stream->isLive();
    }

    public function restore(User $user, Stream $stream)
    {
        return $user->id === $stream->user_id;
    }

    public function forceDelete(User $user, Stream $stream)
    {
        return $user->id === $stream->user_id;
    }
}
