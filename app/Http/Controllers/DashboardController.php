<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = [
            'liveStreams' => $user->streams()->where('status', 'live')->with(['participants'])->get(),
            'upcomingSchedules' => $user->streamSchedules()->where('status', 'scheduled')->orderBy('scheduled_at')->take(5)->get(),
            'recentUploads' => $user->videoUploads()->orderBy('created_at', 'desc')->take(5)->get(),
            'totalStreams' => $user->streams()->count(),
            'totalUploads' => $user->videoUploads()->count(),
            'totalWatchTime' => $this->calculateTotalWatchTime($user),
        ];

        return view('dashboard', $data);
    }

    private function calculateTotalWatchTime($user)
    {
        return $user->streams()
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->get()
            ->sum(function ($stream) {
                return $stream->ended_at->diffInMinutes($stream->started_at);
            });
    }
}
