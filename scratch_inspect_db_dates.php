<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\KasBkm::whereBetween('tanggal', ['2026-08-13', '2026-09-12'])
    ->orderBy('id', 'asc')
    ->get(['id', 'tanggal', 'uraian', 'debet', 'kredit', 'created_at']);

echo "Total: " . $rows->count() . "\n";
echo "First 5 rows:\n";
foreach ($rows->take(5) as $r) {
    echo "  ID {$r->id}: {$r->tanggal} | {$r->uraian} | created={$r->created_at}\n";
}

echo "Last 5 rows:\n";
foreach ($rows->reverse()->take(5)->reverse() as $r) {
    echo "  ID {$r->id}: {$r->tanggal} | {$r->uraian} | created={$r->created_at}\n";
}

// Check how many were created today (2026-09-16) vs before
$today = $rows->filter(fn($r) => $r->created_at && $r->created_at->toDateString() === '2026-09-16');
echo "Created today: " . $today->count() . "\n";
$before = $rows->filter(fn($r) => !$r->created_at || $r->created_at->toDateString() !== '2026-09-16');
echo "Created before: " . $before->count() . "\n";
if ($before->count() > 0) {
    echo "Sample created before:\n";
    foreach ($before->take(5) as $r) {
        echo "  ID {$r->id}: {$r->tanggal} | {$r->uraian} | created={$r->created_at}\n";
    }
}
