<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StreamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'video_upload_id',
        'title',
        'description',
        'scheduled_at',
        'duration',
        'status',
        'auto_start',
        'settings'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'auto_start' => 'boolean',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            $schedule->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videoUpload()
    {
        return $this->belongsTo(VideoUpload::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isDue()
    {
        return $this->scheduled_at <= now();
    }
}
