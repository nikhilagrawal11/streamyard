<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStreamOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $stream = $request->route('stream');

        if ($stream && $stream->user_id !== auth()->id()) {
            // Check if user is a participant
            $participant = $stream->participants()->where('user_id', auth()->id())->first();

            if (!$participant) {
                abort(403, 'You are not authorized to access this stream.');
            }
        }

        return $next($request);
    }
}
