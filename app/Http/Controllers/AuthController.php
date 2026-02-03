<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan form login dan melakukan redirect berbasis peran jika sudah login.
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $role = (Auth::user()?->role) ?? 'user';
            return $role === 'admin'
                ? redirect()->route('dashboard.index')
                : redirect()->route('user.dashboard');
        }

        // Simpan URL sebelumnya untuk redirect setelah login.
        if ($request->has('from')) {
            session(['url.intended' => $request->get('from')]);
        } elseif ($request->headers->get('referer')) {
            $referer = $request->headers->get('referer');
            // Simpan referer hanya jika bukan halaman login.
            if (!str_contains($referer, '/login')) {
                session(['url.intended' => $referer]);
            }
        }

        return view('auth.login');
    }

    /**
     * Verifikasi Google reCAPTCHA v2.
     */
    private function verifyRecaptcha(string $token, string $ip): array
    {
        $secret = config('services.recaptcha.secret_key');
        
        if (empty($secret)) {
            return ['success' => false, 'error' => 'Secret key tidak dikonfigurasi.'];
        }

        // Disable SSL verification untuk development (Windows tanpa CA bundle)
        $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if ($response->failed()) {
            Log::error('reCAPTCHA HTTP error', ['status' => $response->status()]);
            return ['success' => false, 'error' => 'Koneksi ke server captcha gagal.'];
        }

        $data = $response->json();
        Log::info('reCAPTCHA response', $data);

        if (!($data['success'] ?? false)) {
            $errors = $data['error-codes'] ?? [];
            return ['success' => false, 'error' => 'Verifikasi gagal.', 'codes' => $errors];
        }

        return ['success' => true];
    }

    /**
     * Memproses login dan melakukan redirect sesuai peran atau URL tujuan.
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'g-recaptcha-response.required' => 'Silakan centang captcha terlebih dahulu.',
        ]);

        // Verifikasi reCAPTCHA
        $recaptchaResult = $this->verifyRecaptcha(
            $request->input('g-recaptcha-response'),
            $request->ip()
        );

        if (!$recaptchaResult['success']) {
            $errorMsg = $recaptchaResult['error'];
            if (!empty($recaptchaResult['codes'])) {
                $errorMsg .= ' [' . implode(', ', $recaptchaResult['codes']) . ']';
            }
            return back()->withErrors(['captcha' => $errorMsg])->onlyInput('email');
        }

        // Proses login
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = $request->user();
            // Inisialisasi waktu aktivitas terakhir untuk middleware timeout.
            $request->session()->put('last_activity', time());
            
            // Prioritaskan redirect ke URL tujuan jika ada.
            $intendedUrl = session('url.intended');
            if ($intendedUrl) {
                session()->forget('url.intended');
                // Jika asal dari area PNBP, arahkan ke dashboard PNBP.
                $path = parse_url($intendedUrl, PHP_URL_PATH) ?: '';
                if (str_starts_with($path, '/pnbp')) {
                    return redirect()->route('dashboard.index')->with('success', 'Login berhasil!');
                }

                return redirect($intendedUrl)->with('success', 'Login berhasil!');
            }

            // Redirect berdasarkan peran pengguna.
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
     * Memproses logout dan mengembalikan ke beranda.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
