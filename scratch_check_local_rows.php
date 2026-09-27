<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = App\Models\KasBkm::whereBetween('tanggal', ['2026-08-13', '2026-09-12'])->count();
echo "Existing local rows in range 2026-08-13 to 2026-09-12: $count\n";

if ($count > 0) {
    $rows = App\Models\KasBkm::whereBetween('tanggal', ['2026-08-13', '2026-09-12'])->get();
    echo "Sample existing local rows:\n";
    foreach ($rows->take(5) as $r) {
        echo "  ID {$r->id}: {$r->tanggal} | {$r->uraian}\n";
    }
}
