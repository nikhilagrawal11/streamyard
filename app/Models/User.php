<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function streams()
    {
        return $this->hasMany(Stream::class);
    }

    public function streamParticipations()
    {
        return $this->hasMany(StreamParticipant::class);
    }

    public function videoUploads()
    {
        return $this->hasMany(VideoUpload::class);
    }

    public function streamSchedules()
    {
        return $this->hasMany(StreamSchedule::class);
    }

    public function cameraSources()
    {
        return $this->hasMany(CameraSource::class);
    }

    public function liveStreams()
    {
        return $this->streams()->where('status', 'live');
    }

    public function canCreateStream()
    {
        // Add any business logic for stream creation limits
        return true;
    }
}
