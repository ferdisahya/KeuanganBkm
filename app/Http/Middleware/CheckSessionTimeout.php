<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $loginAt = session('login_at');

            if (!$loginAt) {
                // Set timestamp for existing active sessions
                session(['login_at' => now()->timestamp]);
            } else {
                $twentyFourHoursInSeconds = 86400; // 24 jam x 3600 detik
                if (now()->timestamp - $loginAt > $twentyFourHoursInSeconds) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir (otomatis logout setelah 24 jam demi keamanan). Silakan login kembali.');
                }
            }
        }

        return $next($request);
    }
}
