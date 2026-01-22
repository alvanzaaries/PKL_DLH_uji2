<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateInternalApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        $validApiKey = config('app.internal_api_key');

        // Jika API key tidak dikonfigurasi, tolak semua request
        if (empty($validApiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key not configured on server',
                'error_code' => 'SERVER_MISCONFIGURATION'
            ], 500);
        }

        // Validasi API key
        if (empty($apiKey) || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API key. Please provide a valid X-API-Key header.',
                'error_code' => 'UNAUTHORIZED'
            ], 401);
        }

        return $next($request);
    }
}
