<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Streaming Settings
    |--------------------------------------------------------------------------
    */

    'default_resolution' => env('STREAMING_DEFAULT_RESOLUTION', '1280x720'),
    'default_fps' => env('STREAMING_DEFAULT_FPS', 30),
    'default_bitrate' => env('STREAMING_DEFAULT_BITRATE', '2500k'),

    /*
    |--------------------------------------------------------------------------
    | RTMP Server Settings
    |--------------------------------------------------------------------------
    */

    'rtmp' => [
        'host' => env('RTMP_HOST', 'localhost'),
        'port' => env('RTMP_PORT', 1935),
        'app' => env('RTMP_APP', 'live'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HLS Settings
    |--------------------------------------------------------------------------
    */

    'hls' => [
        'host' => env('HLS_HOST', 'localhost'),
        'port' => env('HLS_PORT', 8080),
        'segment_duration' => env('HLS_SEGMENT_DURATION', 6),
        'playlist_length' => env('HLS_PLAYLIST_LENGTH', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Processing Settings
    |--------------------------------------------------------------------------
    */

    'processing' => [
        'max_file_size' => env('MAX_VIDEO_SIZE', 1048576), // 1GB in KB
        'allowed_formats' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'],
        'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
        'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WebRTC Configuration
    |--------------------------------------------------------------------------
    */

    'webrtc' => [
        'ice_servers' => [
            ['urls' => 'stun:stun.l.google.com:19302'],
            ['urls' => 'stun:stun1.l.google.com:19302'],
// Add TURN servers for production
// [
//     'urls' => 'turn:your-turn-server.com:3478',
//     'username' => 'username',
//     'credential' => 'password'
// ]
        ],
        'constraints' => [
            'audio' => true,
            'video' => [
                'width' => ['min' => 640, 'ideal' => 1280, 'max' => 1920],
                'height' => ['min' => 480, 'ideal' => 720, 'max' => 1080],
                'frameRate' => ['min' => 15, 'ideal' => 30, 'max' => 60]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Stream Limits
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'max_participants_per_stream' => env('MAX_PARTICIPANTS_PER_STREAM', 50),
        'max_streams_per_user' => env('MAX_STREAMS_PER_USER', 10),
        'max_duration_minutes' => env('MAX_STREAM_DURATION', 480), // 8 hours
    ],

];
