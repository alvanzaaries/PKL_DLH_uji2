<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form (HEAD logic with role-based redirect)
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $role = (Auth::user()?->role) ?? 'user';
            return $role === 'admin'
                ? redirect()->route('dashboard.index')
                : redirect()->route('user.dashboard');
        }

        // Simpan URL sebelumnya untuk redirect setelah login (dari Incoming)
        if ($request->has('from')) {
            session(['url.intended' => $request->get('from')]);
        } elseif ($request->headers->get('referer')) {
            $referer = $request->headers->get('referer');
            // Only store referer if it's not the login page itself
            if (!str_contains($referer, '/login')) {
                session(['url.intended' => $referer]);
            }
        }

        return view('auth.login');
    }

    /**
     * Handle login (HEAD logic with role-based redirect)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            
            // Check for intended URL first (from Incoming feature)
            $intendedUrl = session('url.intended');
            if ($intendedUrl) {
                session()->forget('url.intended');
                // If user came from PNBP area, send to PNBP dashboard (named route)
                $path = parse_url($intendedUrl, PHP_URL_PATH) ?: '';
                if (str_starts_with($path, '/pnbp')) {
                    return redirect()->route('dashboard.index')->with('success', 'Login berhasil!');
                }

                return redirect($intendedUrl)->with('success', 'Login berhasil!');
            }

            // Role-based redirect (from HEAD)
            if (($user->role ?? 'user') === 'admin') {
                return redirect()->route('dashboard.index');
            }

            return redirect()->route('user.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout (HEAD logic - redirect to home)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
