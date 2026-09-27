<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sql = file_get_contents('import_kas_bkm_13agu_12sep2026.sql');

\DB::beginTransaction();
try {
    // Run the script
    \DB::unprepared($sql);
    echo "SQL executed without any syntax errors!\n";

    // Verify Kas BKM records inserted
    $inserted = \DB::table('kas_bkm')
        ->whereBetween('tanggal', ['2026-08-13', '2026-09-12'])
        ->get();

    echo "Total Kas BKM inserted between 2026-08-13 and 2026-09-12: " . $inserted->count() . "\n";

    // Check if any foreign keys are null
    $nullRek = $inserted->whereNull('rekening_bkm_id')->count();
    $nullSub = $inserted->whereNull('subrekening_bkm_id')->count();
    $nullItem = $inserted->whereNull('item_bkm_id')->count();
    echo "Null Rekening IDs: $nullRek, Null Subrekening IDs: $nullSub, Null Item IDs: $nullItem\n";

    $totalDebet = $inserted->sum('debet');
    $totalKredit = $inserted->sum('kredit');
    echo "Total Debet: " . number_format($totalDebet, 2, ',', '.') . "\n";
    echo "Total Kredit: " . number_format($totalKredit, 2, ',', '.') . "\n";

    // Check first and last inserted record
    $first = $inserted->first();
    $last = $inserted->last();
    echo "First row (#{$first->id}): {$first->tanggal} | {$first->uraian} | debet={$first->debet} | kredit={$first->kredit} | saldo={$first->saldo_kas}\n";
    echo "Last row (#{$last->id}): {$last->tanggal} | {$last->uraian} | debet={$last->debet} | kredit={$last->kredit} | saldo={$last->saldo_kas}\n";

    // Rollback so we don't pollute local DB unless desired
    \DB::rollBack();
    echo "Transaction rolled back successfully for testing cleanliness!\n";
} catch (\Throwable $e) {
    \DB::rollBack();
    echo "ERROR during execution: " . $e->getMessage() . "\n";
}
