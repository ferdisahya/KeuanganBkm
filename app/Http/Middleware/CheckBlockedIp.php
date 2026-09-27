<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DeviceLockout;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $lockout = DeviceLockout::where('ip_address', $ip)->first();

        if ($lockout && $lockout->isCurrentlyBlocked()) {
            $remaining = $lockout->blocked_until->diffForHumans([
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            ]);

            $message = "Akses Ditolak! Device/IP Anda ({$ip}) sedang DIBLOKIR hingga " . $lockout->blocked_until->format('d M Y H:i') . " (Sisa: {$remaining}). Alasan: {$lockout->block_reason}. Silakan hubungi Project Manager, Direktur Operasional, atau Direktur untuk membuka blokir.";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            if ($request->is('login') || $request->routeIs('login')) {
                return response(view('auth.login', ['blockedMessage' => $message]), 403);
            }

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
