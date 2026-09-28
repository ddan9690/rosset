<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Throttle updates: only update if last_active_at is null or older than 5 minutes
            if (!$user->last_active_at || $user->last_active_at->lt(now()->subMinutes(5))) {
                $user->update(['last_active_at' => now()]);
            }
        }

        return $next($request);
    }
}