<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeviceLockout;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman Google OAuth dengan Account Picker.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account']) // Selalu tampilkan pemilih akun
            ->redirect();
    }

    /**
     * Tangani callback dari Google OAuth.
     * Pengguna yang berhasil autentikasi Google akan otomatis dibuat/diperbarui di database.
     */
    public function handleGoogleCallback()
    {
        $ip = request()->ip();

        // Pengecekan awal pemblokiran IP
        $lockout = DeviceLockout::where('ip_address', $ip)->first();
        if ($lockout && $lockout->isCurrentlyBlocked()) {
            $remaining = $lockout->blocked_until->diffForHumans([
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            ]);
            return redirect()->route('login')->with('error', "Akses Ditolak! IP Device Anda ({$ip}) sedang DIBLOKIR hingga " . $lockout->blocked_until->format('d M Y H:i') . " (Sisa: {$remaining}). Silakan hubungi Project Manager, Direktur Operasional, atau Direktur untuk membuka blokir.");
        }

        try {
            $driver = Socialite::driver('google');
            
            // Bypass SSL verification in local environment to fix cURL error 60
            if (app()->environment('local')) {
                $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $googleUser = $driver->user();

            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update nama jika belum diset
                if (empty($user->name)) {
                    $user->update(['name' => $googleUser->name]);
                }

                // Reset percobaan gagal karena login berhasil
                $this->resetFailedLoginAttempt($ip);
            } else {
                // Catat percobaan gagal karena email tidak terdaftar
                $blockMsg = $this->recordFailedLoginAttempt($ip);
                $errorMsg = 'Akses Ditolak! Akun email Anda (' . $googleUser->email . ') belum didaftarkan.';
                if ($blockMsg) {
                    $errorMsg .= ' ' . $blockMsg;
                }

                return redirect()->route('login')->with('error', $errorMsg);
            }

            Auth::login($user, true); // true = remember
            session(['login_at' => now()->timestamp]);

            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!')
                ->with('greeting', 'Halo, ' . $user->name . '! 👋');
        } catch (Exception $e) {
            $blockMsg = $this->recordFailedLoginAttempt($ip);
            $errorMsg = 'Gagal login menggunakan Google: ' . $e->getMessage();
            if ($blockMsg) {
                $errorMsg .= ' ' . $blockMsg;
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }
    }

    /**
     * Bypass login untuk pengembangan lokal.
     */
    public function bypassLogin()
    {
        if (config('app.env') !== 'local') {
            abort(403, 'Hanya diizinkan di lokal');
        }

        $ip = request()->ip();

        // Pengecekan awal pemblokiran IP
        $lockout = DeviceLockout::where('ip_address', $ip)->first();
        if ($lockout && $lockout->isCurrentlyBlocked()) {
            $remaining = $lockout->blocked_until->diffForHumans([
                'parts' => 2,
                'join' => true,
                'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
            ]);
            return redirect()->route('login')->with('error', "Akses Ditolak! IP Device Anda ({$ip}) sedang DIBLOKIR hingga " . $lockout->blocked_until->format('d M Y H:i') . " (Sisa: {$remaining}). Silakan hubungi Project Manager, Direktur Operasional, atau Direktur untuk membuka blokir.");
        }

        // Cari user dengan role Admin Keuangan terlebih dahulu
        $user = User::where('role', 'Admin Keuangan')->first();

        // Jika tidak ada, ambil user pertama dan promosikan
        if (!$user) {
            $user = User::first();
            if ($user) {
                $user->update(['role' => 'Admin Keuangan']);
            }
        }

        // Jika benar-benar kosong, buat baru
        if (!$user) {
            $user = User::create([
                'name'      => 'Admin Keuangan',
                'email'     => 'admin@ptbkm.com',
                'role'      => 'Admin Keuangan',
            ]);
        }

        // Reset percobaan gagal untuk IP ini
        $this->resetFailedLoginAttempt($ip);

        Auth::login($user);
        session(['login_at' => now()->timestamp]);

        return redirect()->route('dashboard')
            ->with('success', 'Masuk via Bypass Developer Mode!')
            ->with('greeting', 'Halo, ' . $user->name . '!');
    }

    /**
     * Logout pengguna.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Berhasil logout!');
    }

    /**
     * Catat percobaan gagal login berdasarkan IP device.
     * Aturan Pemblokiran:
     * - 3x Gagal: Diblokir selama 3 Jam.
     * - Total 5x Gagal: Diblokir selama 5 Hari.
     */
    protected function recordFailedLoginAttempt(string $ip): ?string
    {
        $lockout = DeviceLockout::firstOrCreate(
            ['ip_address' => $ip],
            ['failed_attempts' => 0]
        );

        // Jika pernah di-unblock atau blokir lama sudah lewat, update failed_attempts
        if ($lockout->unblocked_at !== null && ($lockout->blocked_until === null || $lockout->blocked_until->isPast())) {
            $lockout->unblocked_at = null;
            $lockout->unblocked_by = null;
            $lockout->failed_attempts = 0;
        }

        $lockout->failed_attempts += 1;
        $attempts = $lockout->failed_attempts;
        $blockMessage = null;

        if ($attempts >= 5) {
            $lockout->blocked_until = now()->addDays(5);
            $lockout->block_reason = 'Gagal login 5 kali (Blokir 5 Hari)';
            $blockMessage = "⚠️ IP Device Anda ({$ip}) telah DIBLOKIR SELAMA 5 HARI karena gagal login 5 kali. Silakan hubungi Project Manager, Direktur Operasional, atau Direktur.";
        } elseif ($attempts >= 3) {
            $lockout->blocked_until = now()->addHours(3);
            $lockout->block_reason = 'Gagal login 3 kali (Blokir 3 Jam)';
            $blockMessage = "⚠️ IP Device Anda ({$ip}) telah DIBLOKIR SEMENTARA SELAMA 3 JAM karena gagal login 3 kali. Silakan hubungi Project Manager, Direktur Operasional, atau Direktur.";
        }

        $lockout->save();

        return $blockMessage;
    }

    /**
     * Reset status percobaan gagal jika login berhasil.
     */
    protected function resetFailedLoginAttempt(string $ip): void
    {
        $lockout = DeviceLockout::where('ip_address', $ip)->first();
        if ($lockout && !$lockout->isCurrentlyBlocked()) {
            $lockout->update([
                'failed_attempts' => 0,
                'blocked_until' => null,
                'block_reason' => null,
            ]);
        }
    }
}
