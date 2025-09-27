<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'stream_id',
        'user_id',
        'username',
        'message',
        'type',
        'metadata',
        'sent_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->uuid = (string) Str::uuid();
            $message->sent_at = now();
        });
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
