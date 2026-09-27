<?php
// clear.php - Utility script to clear Laravel cache on Hostinger shared hosting

define('LARAVEL_START', microtime(true));

// Load Composer Autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel Application
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;

$results = [];

try {
    Artisan::call('config:clear');
    $results[] = '✓ Config cache cleared (php artisan config:clear)';
} catch (\Throwable $e) {
    $results[] = '✗ Config clear error: ' . $e->getMessage();
}

try {
    Artisan::call('cache:clear');
    $results[] = '✓ Application cache cleared (php artisan cache:clear)';
} catch (\Throwable $e) {
    $results[] = '✗ Cache clear error: ' . $e->getMessage();
}

try {
    Artisan::call('view:clear');
    $results[] = '✓ Compiled views cleared (php artisan view:clear)';
} catch (\Throwable $e) {
    $results[] = '✗ View clear error: ' . $e->getMessage();
}

try {
    Artisan::call('route:clear');
    $results[] = '✓ Route cache cleared (php artisan route:clear)';
} catch (\Throwable $e) {
    $results[] = '✗ Route clear error: ' . $e->getMessage();
}

try {
    Artisan::call('storage:link');
    $results[] = '✓ Storage symlink verified (php artisan storage:link)';
} catch (\Throwable $e) {
    // Attempt manual symlink if artisan failed
    if (!file_exists(__DIR__ . '/storage')) {
        @symlink(__DIR__ . '/../storage/app/public', __DIR__ . '/storage');
    }
    $results[] = '✓ Storage symlink process executed';
}

if (function_exists('opcache_reset')) {
    @opcache_reset();
    $results[] = '✓ OPcache reset';
}

// Clear any files in bootstrap/cache except .gitignore
$cacheFiles = glob(__DIR__.'/../bootstrap/cache/*.php');
if ($cacheFiles) {
    foreach ($cacheFiles as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
    $results[] = '✓ bootstrap/cache/*.php files purged';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clear Cache System - PT BKM</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .card {
            background: #1e293b;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid #334155;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon {
            width: 56px;
            height: 56px;
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px;
            color: #ffffff;
        }
        p {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 24px;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0 0 24px;
            text-align: left;
        }
        li {
            padding: 12px 16px;
            background: #0f172a;
            margin-bottom: 8px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: #4ade80;
            border: 1px solid rgba(74, 222, 128, 0.15);
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: #7a0f1d;
            color: #ffffff;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .btn:hover {
            background: #961526;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
            </svg>
        </div>
        <h1>Cache Berhasil Dibersihkan!</h1>
        <p>Sistem frontend dan backend telah direfresh secara komplit.</p>
        <ul>
            <?php foreach($results as $res): ?>
                <li><?= htmlspecialchars($res) ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="/" class="btn">Kembali ke Aplikasi Keuangan</a>
    </div>
</body>
</html>
