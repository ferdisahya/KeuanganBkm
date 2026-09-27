<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KasRaab;

echo "=== KasRaab Sample Rows ===\n";
$rows = KasRaab::where('uraian', 'like', '%Ongkos gendong%')
    ->orWhere('uraian', 'like', '%Penggantian biaya%')
    ->get();

foreach ($rows as $r) {
    echo "ID: {$r->id} | Tanggal: {$r->tanggal} | Uraian: {$r->uraian} | Debet: {$r->debet} | Kredit: {$r->kredit} | Saldo: {$r->saldo}\n";
}
