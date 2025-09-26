<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VideoUpload;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoUploadPolicy
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

    public function view(User $user, VideoUpload $videoUpload)
    {
        return $user->id === $videoUpload->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, VideoUpload $videoUpload)
    {
        return $user->id === $videoUpload->user_id;
    }

    public function delete(User $user, VideoUpload $videoUpload)
    {
        return $user->id === $videoUpload->user_id;
    }

    public function restore(User $user, VideoUpload $videoUpload)
    {
        return $user->id === $videoUpload->user_id;
    }

    public function forceDelete(User $user, VideoUpload $videoUpload)
    {
        return $user->id === $videoUpload->user_id;
    }
}
