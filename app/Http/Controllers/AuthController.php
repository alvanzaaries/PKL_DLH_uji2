<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }
        
        // Simpan URL sebelumnya untuk redirect setelah login
        if ($request->has('from')) {
            session(['url.intended' => $request->get('from')]);
        } elseif ($request->headers->get('referer')) {
            session(['url.intended' => $request->headers->get('referer')]);
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Ambil URL intended dari session
            $intendedUrl = session('url.intended');
            
            if ($intendedUrl) {
                session()->forget('url.intended');
                return redirect($intendedUrl)->with('success', 'Login berhasil!');
            }
            
            // Default ke dashboard jika tidak ada URL intended
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return back()->with('success', 'Logout berhasil!');
    }
}
