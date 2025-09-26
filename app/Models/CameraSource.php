<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CameraSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'stream_id',
        'user_id',
        'source_name',
        'source_type',
        'device_id',
        'is_active',
        'is_primary',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($source) {
            $source->uuid = (string) Str::uuid();
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

    public function activate()
    {
        // Deactivate other sources for this stream
        $this->stream->cameraSources()->where('id', '!=', $this->id)->update(['is_active' => false]);

        // Activate this source
        $this->update(['is_active' => true]);
    }
}
