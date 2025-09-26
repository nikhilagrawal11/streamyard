<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VideoUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'description',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'duration',
        'thumbnail_path',
        'status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($upload) {
            $upload->uuid = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function streamSchedules()
    {
        return $this->hasMany(StreamSchedule::class);
    }

    public function getFilePathAttribute()
    {
        return 'videos/' . $this->filename;
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function isProcessed()
    {
        return $this->status === 'ready';
    }
}
