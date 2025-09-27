<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'user_id',
        'status',
        'type',
        'stream_key',
        'rtmp_url',
        'playback_url',
        'recording_path',
        'settings',
        'scheduled_at',
        'started_at',
        'ended_at',
        'viewer_count',
        'max_participants'
    ];

    protected $casts = [
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stream) {
            $stream->uuid = (string) Str::uuid();
            $stream->stream_key = Str::random(32);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(StreamParticipant::class);
    }

    public function cameraSources()
    {
        return $this->hasMany(CameraSource::class);
    }

    public function schedules()
    {
        return $this->hasMany(StreamSchedule::class);
    }

    public function isLive()
    {
        return $this->status === 'live';
    }

    public function canJoin()
    {
        return in_array($this->status, ['scheduled', 'live']);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
