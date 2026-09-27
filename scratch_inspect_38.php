<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$before = App\Models\KasBkm::whereBetween('tanggal', ['2026-08-13', '2026-09-12'])
    ->where(function($q) {
        $q->whereNull('created_at')->orWhere('created_at', '<', '2026-09-16');
    })
    ->orderBy('tanggal', 'asc')
    ->get(['id', 'tanggal', 'uraian', 'debet', 'kredit', 'saldo_kas']);

echo "Total 38 old rows:\n";
foreach ($before as $r) {
    echo "  ID {$r->id} | {$r->tanggal} | {$r->uraian} | debet={$r->debet} | kredit={$r->kredit} | saldo={$r->saldo_kas}\n";
}
