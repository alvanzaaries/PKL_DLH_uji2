<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     * If the user is authenticated, ensure their session hasn't been idle
     * longer than the configured session lifetime. If timed out, logout.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lifetimeMinutes = (int) config('session.lifetime', 120);
            $lifetimeSeconds = $lifetimeMinutes * 60;

            $last = (int) Session::get('last_activity', time());
            $now = time();

            if (($now - $last) > $lifetimeSeconds) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('message', 'Sesi Anda telah berakhir karena tidak aktif.');
            }

            // update last activity timestamp
            Session::put('last_activity', $now);
        }

        return $next($request);
    }
}
