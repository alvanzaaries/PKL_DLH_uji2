<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,user')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Laravel passes middleware params split by comma, e.g. 'role:admin,user' => ['admin','user']
        // Also accept a single string containing commas just in case.
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', (string) $r) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }
        $allowed = array_values(array_unique($allowed));
        $role = (string) ($user->role ?? 'user');

        if (!in_array($role, $allowed, true)) {
            if ($role === 'user') {
                return redirect()->route('user.dashboard');
            }

            if ($role === 'admin') {
                return redirect()->route('dashboard.index');
            }

            abort(403, 'Tidak punya akses.');
        }

        return $next($request);
    }
}
