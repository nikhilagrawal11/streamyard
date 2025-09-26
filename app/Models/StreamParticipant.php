<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream_id',
        'user_id',
        'role',
        'status',
        'participant_name',
        'participant_email',
        'camera_enabled',
        'microphone_enabled',
        'screen_sharing',
        'video_settings',
        'joined_at',
        'left_at'
    ];

    protected $casts = [
        'camera_enabled' => 'boolean',
        'microphone_enabled' => 'boolean',
        'screen_sharing' => 'boolean',
        'video_settings' => 'array',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isHost()
    {
        return $this->role === 'host';
    }

    public function isActive()
    {
        return $this->status === 'joined';
    }
}
