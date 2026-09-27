<?php

namespace App\Http\Controllers;

use App\Helpers\LogistikStorageHelper;
use Illuminate\Http\Request;

class StorageFileController extends Controller
{
    /**
     * Serve uploaded storage files with robust fallback paths across Keuangan and Logistik.
     * Handles /storage/{path} and /logistik-file/{path} requests even when public/storage symlink is missing on hosting.
     */
    public function show(Request $request, $path)
    {
        // Prevent path traversal
        if (str_contains($path, '..')) {
            abort(400, 'Invalid file path request.');
        }

        $fullPath = LogistikStorageHelper::findFile($path);

        if ($fullPath && file_exists($fullPath) && is_file($fullPath)) {
            $mimeType = @mime_content_type($fullPath) ?: 'application/octet-stream';

            // Override extension-based mime types if detection is generic
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if ($mimeType === 'application/octet-stream' || empty($mimeType)) {
                $map = [
                    'pdf' => 'application/pdf',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                ];
                if (isset($map[$ext])) {
                    $mimeType = $map[$ext];
                }
            }

            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404, 'File lampiran "' . htmlspecialchars(basename($path)) . '" tidak ditemukan pada server.');
    }
}

