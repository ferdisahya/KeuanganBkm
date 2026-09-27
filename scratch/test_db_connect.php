<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $usersCount = DB::connection('mysql')->table('users')->count();
    echo "SUCCESS connecting to Main DB (u957149181_keuangan). Users count: {$usersCount}\n";
} catch (\Throwable $e) {
    echo "ERROR connecting to Main DB: " . $e->getMessage() . "\n";
}

try {
    $pembelianCount = DB::connection('mysql_logistik')->table('pembelian')->count();
    $kebutuhanCount = DB::connection('mysql_logistik')->table('kebutuhan_langsung')->count();
    echo "SUCCESS connecting to Logistik DB (u957149181_logistik_bkm).\n";
    echo "  - Pembelian count: {$pembelianCount}\n";
    echo "  - Kebutuhan Langsung count: {$kebutuhanCount}\n";
} catch (\Throwable $e) {
    echo "ERROR connecting to Logistik DB: " . $e->getMessage() . "\n";
}
