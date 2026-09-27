<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class LogistikStorageHelper
{
    /**
     * Get candidate directories for Logistik public storage.
     * Evaluates .env overrides, Hostinger server structure (e.g. web_logistik/web_logistik), and local Laragon paths.
     */
    public static function getCandidateStorageRoots(): array
    {
        $roots = [];

        // 1. Explicit env settings
        if (env('LOGISTIK_STORAGE_PATH')) {
            $roots[] = rtrim(env('LOGISTIK_STORAGE_PATH'), '/\\');
        }

        if (env('LOGISTIK_PATH')) {
            $base = rtrim(env('LOGISTIK_PATH'), '/\\');
            $roots[] = $base . '/storage/app/public';
            $roots[] = $base . '/public/storage';
        }

        // Folder name variations used for Logistik project
        // Note: Hostinger screenshot confirms: web_logistik/web_logistik
        $logistikFolders = [
            'web_logistik/web_logistik',
            'web-logistik/web-logistik',
            'web_logistik',
            'web-logistik',
            'bkm_logistik/bkm_logistik',
            'bkm-logistik/bkm-logistik',
            'bkm_logistik',
            'bkm-logistik',
            'logistik/logistik',
            'logistik',
        ];

        $base = base_path();

        // 2. Linux / Hostinger home directory detection: /home/uXXXXXXX
        if (preg_match('#^(/home/[^/]+)#', $base, $matches)) {
            $userHome = $matches[1];
            foreach ($logistikFolders as $folder) {
                $roots[] = $userHome . '/' . $folder . '/storage/app/public';
                $roots[] = $userHome . '/public_html/' . $folder . '/storage/app/public';
            }
        }

        // 3. Relative paths from Laravel base_path() (1 to 4 levels up)
        $relativeBases = [
            $base . '/..',
            $base . '/../..',
            $base . '/../../..',
            $base . '/../../../..',
        ];

        foreach ($relativeBases as $relBase) {
            foreach ($logistikFolders as $folder) {
                $roots[] = $relBase . '/' . $folder . '/storage/app/public';
            }
        }

        // 4. Candidate paths relative to $_SERVER['DOCUMENT_ROOT'] if available
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $docBases = [
                $docRoot . '/..',
                $docRoot . '/../..',
                $docRoot . '/../../..',
            ];
            foreach ($docBases as $dBase) {
                foreach ($logistikFolders as $folder) {
                    $roots[] = $dBase . '/' . $folder . '/storage/app/public';
                }
            }
        }

        // 5. Windows Laragon default fallback
        $parent = dirname($base);
        foreach ($logistikFolders as $folder) {
            $roots[] = $parent . '/' . $folder . '/storage/app/public';
        }

        // 6. Keuangan own storage fallbacks
        $roots[] = storage_path('app/public');
        $roots[] = storage_path('app');
        $roots[] = public_path('storage');
        $roots[] = base_path('storage');

        // Remove duplicates and normalize slashes
        $normalized = [];
        foreach ($roots as $root) {
            $norm = str_replace('\\', '/', $root);
            if (!in_array($norm, $normalized)) {
                $normalized[] = $norm;
            }
        }

        return $normalized;
    }

    /**
     * Detect the active Logistik storage/app/public directory.
     * Returns the first existing directory, or the best candidate for saving uploads.
     */
    public static function getLogistikStoragePublicPath(): string
    {
        $candidates = self::getCandidateStorageRoots();

        foreach ($candidates as $candidate) {
            // Priority to logistik-specific folders that exist
            if (is_dir($candidate) && (str_contains($candidate, 'logistik') || str_contains($candidate, 'web_logistik') || str_contains($candidate, 'web-logistik'))) {
                return $candidate;
            }
        }

        // Check any candidate that exists
        foreach ($candidates as $candidate) {
            if (is_dir($candidate)) {
                return $candidate;
            }
        }

        // Best fallback if not yet created on disk
        if (preg_match('#^(/home/[^/]+)#', base_path(), $matches)) {
            return $matches[1] . '/web_logistik/web_logistik/storage/app/public';
        }

        return base_path('../web_logistik/web_logistik/storage/app/public');
    }

    /**
     * Find a file path on disk across Logistik and Keuangan storage locations.
     * Handles spelling variations ('settlement' vs 'settlemant') and path sanitization.
     */
    public static function findFile(string $path): ?string
    {
        $cleanPath = ltrim(urldecode($path), '/\\');

        // Strip leading storage/ or public/
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        if (str_starts_with($cleanPath, 'public/')) {
            $cleanPath = substr($cleanPath, 7);
        }

        $cleanPath = ltrim($cleanPath, '/\\');

        if (empty($cleanPath)) {
            return null;
        }

        // Generate path variations (handle settlement vs settlemant)
        $variations = [$cleanPath];

        if (str_contains($cleanPath, 'settlement')) {
            $variations[] = str_replace('settlement', 'settlemant', $cleanPath);
        } elseif (str_contains($cleanPath, 'settlemant')) {
            $variations[] = str_replace('settlemant', 'settlement', $cleanPath);
        }

        // In case path is just the filename without subfolder
        if (!str_contains($cleanPath, '/')) {
            $variations[] = 'transfer/' . $cleanPath;
            $variations[] = 'settlement/' . $cleanPath;
            $variations[] = 'settlemant/' . $cleanPath;
        }

        $candidates = self::getCandidateStorageRoots();

        foreach ($variations as $variation) {
            foreach ($candidates as $root) {
                $fullPath = rtrim($root, '/\\') . '/' . ltrim($variation, '/\\');
                if (file_exists($fullPath) && is_file($fullPath)) {
                    return realpath($fullPath) ?: $fullPath;
                }
            }
        }

        return null;
    }

    /**
     * Save an uploaded Bukti Transfer file.
     * Saves to BOTH Logistik's storage/app/public/transfer AND Keuangan's storage/app/public/transfer.
     *
     * @param UploadedFile $file
     * @param string $directory Default 'transfer'
     * @return string Relative path stored in database (e.g. 'transfer/random40.jpg')
     */
    public static function saveTransferFile(UploadedFile $file, string $directory = 'transfer'): string
    {
        $extension = $file->getClientOriginalExtension();
        if (empty($extension)) {
            $extension = 'png';
        }
        $filename = Str::random(40) . '.' . strtolower($extension);
        $relativePath = $directory . '/' . $filename;

        // 1. Save directly into Keuangan local public storage
        try {
            $file->storeAs($directory, $filename, 'public');
        } catch (\Throwable $e) {
            // Fallback manual copy to Keuangan storage
            $localDir = storage_path('app/public/' . $directory);
            if (!is_dir($localDir)) {
                @mkdir($localDir, 0775, true);
            }
            @copy($file->getRealPath(), $localDir . '/' . $filename);
        }

        // 2. Save directly into Logistik public storage directory (e.g. web_logistik/web_logistik/storage/app/public/transfer)
        try {
            $logistikRoot = self::getLogistikStoragePublicPath();
            $logistikTargetDir = rtrim($logistikRoot, '/\\') . '/' . $directory;

            if (!is_dir($logistikTargetDir)) {
                @mkdir($logistikTargetDir, 0775, true);
            }

            $keuanganStored = storage_path('app/public/' . $relativePath);
            $source = (file_exists($keuanganStored) && is_file($keuanganStored))
                ? $keuanganStored
                : $file->getRealPath();

            if (file_exists($source) && is_file($source)) {
                @copy($source, $logistikTargetDir . '/' . $filename);
            }
        } catch (\Throwable $e) {
            // Non-blocking log if permission issue on server
            \Log::warning("Gagal menyalin bukti transfer ke logistik directory: " . $e->getMessage());
        }

        return $relativePath;
    }
}
