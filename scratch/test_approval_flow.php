<?php

require 'c:/laragon/www/keuangan/vendor/autoload.php';
$app = require_once 'c:/laragon/www/keuangan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormAjukanDanaBkm;
use App\Models\FormAjukanDanaRaab;
use App\Models\User;
use App\Http\Controllers\FormAjukanDanaBkmController;
use App\Http\Controllers\FormAjukanDanaRaabController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== START APPROVAL WORKFLOW TEST ===\n";

// Find or mock users with each role
$pmUser = User::where('role', 'Project Manager')->first() ?? User::factory()->make(['role' => 'Project Manager', 'id' => 9991]);
$doUser = User::where('role', 'Direktur Operasional')->first() ?? User::factory()->make(['role' => 'Direktur Operasional', 'id' => 9992]);
$dirUser = User::where('role', 'Direktur')->first() ?? User::factory()->make(['role' => 'Direktur', 'id' => 9993]);

echo "Users for testing:\n";
echo "- PM: {$pmUser->name} ({$pmUser->role})\n";
echo "- DO: {$doUser->name} ({$doUser->role})\n";
echo "- Direktur: {$dirUser->name} ({$dirUser->role})\n\n";

// TEST CASE 1: Nominal < 2 Juta (Rp 1.500.000)
echo "--- TEST 1: Nominal Rp 1.500.000 (< 2 Jt) ---\n";
$item1 = FormAjukanDanaBkm::create([
    'tanggal' => date('Y-m-d'),
    'file' => 'upload/pengajuan/test1.pdf',
    'nominal' => 1500000,
    'status' => 'Pending',
]);

assert($item1->needsPmApproval() === true, "Needs PM should be true");
assert($item1->needsDoApproval() === false, "Needs DO should be false");
assert($item1->needsDirApproval() === false, "Needs Dir should be false");
assert($item1->canUserApprove($pmUser) === true, "PM can approve");
assert($item1->canUserApprove($doUser) === false, "DO cannot approve");
assert($item1->canUserApprove($dirUser) === false, "Dir cannot approve");

// PM Approves
$item1->pm_approved_at = now();
$item1->pm_approved_by = $pmUser->id;
$item1->checkAndUpdateStatus();
echo "Status after PM approve: {$item1->status}\n";
assert($item1->status === 'Approved All', "Status must be Approved All!");
echo "[PASS] Test 1 (< 2 Juta) passed!\n\n";

// TEST CASE 2: Nominal Rp 5.000.000 (2 Jt - 10 Jt)
echo "--- TEST 2: Nominal Rp 5.000.000 (2 Jt - 10 Jt) ---\n";
$item2 = FormAjukanDanaBkm::create([
    'tanggal' => date('Y-m-d'),
    'file' => 'upload/pengajuan/test2.pdf',
    'nominal' => 5000000,
    'status' => 'Pending',
]);

assert($item2->needsPmApproval() === true, "Needs PM should be true");
assert($item2->needsDoApproval() === true, "Needs DO should be true");
assert($item2->needsDirApproval() === false, "Needs Dir should be false");

// PM Approves first
$item2->pm_approved_at = now();
$item2->pm_approved_by = $pmUser->id;
$item2->checkAndUpdateStatus();
echo "Status after PM approve only: {$item2->status}\n";
assert($item2->status === 'Pending', "Status must still be Pending!");

// Now DO Approves
$item2->do_approved_at = now();
$item2->do_approved_by = $doUser->id;
$item2->checkAndUpdateStatus();
echo "Status after DO approve: {$item2->status}\n";
assert($item2->status === 'Approved All', "Status must now be Approved All!");
echo "[PASS] Test 2 (2 - 10 Juta) passed!\n\n";

// TEST CASE 3: Nominal Rp 25.000.000 (> 10 Juta)
echo "--- TEST 3: Nominal Rp 25.000.000 (> 10 Jt) ---\n";
$item3 = FormAjukanDanaRaab::create([
    'tanggal' => date('Y-m-d'),
    'file' => 'upload/pengajuan/test3.pdf',
    'nominal' => 25000000,
    'status' => 'Pending',
]);

assert($item3->needsPmApproval() === true);
assert($item3->needsDoApproval() === true);
assert($item3->needsDirApproval() === true);

// PM Approves
$item3->pm_approved_at = now();
$item3->pm_approved_by = $pmUser->id;
$item3->checkAndUpdateStatus();
assert($item3->status === 'Pending');

// DO Approves
$item3->do_approved_at = now();
$item3->do_approved_by = $doUser->id;
$item3->checkAndUpdateStatus();
assert($item3->status === 'Pending');

// Direktur Approves
$item3->dir_approved_at = now();
$item3->dir_approved_by = $dirUser->id;
$item3->checkAndUpdateStatus();
echo "Status after PM + DO + Dir approve: {$item3->status}\n";
assert($item3->status === 'Approved All', "Status must be Approved All!");
echo "[PASS] Test 3 (> 10 Juta) passed!\n\n";

// TEST CASE 4: Controller Index Render Test
echo "--- TEST 4: Controller Index Views Render ---\n";
Auth::login($pmUser);

$bkmController = new FormAjukanDanaBkmController();
$viewBkm = $bkmController->index(new Request());
$htmlBkm = $viewBkm->render();
assert(strpos($htmlBkm, 'Form Ajukan Dana BKM') !== false, "Must contain title");
echo "[PASS] BKM Index View renders correctly (length: " . strlen($htmlBkm) . ")\n";

$raabController = new FormAjukanDanaRaabController();
$viewRaab = $raabController->index(new Request());
$htmlRaab = $viewRaab->render();
assert(strpos($htmlRaab, 'Form Ajukan Dana RAAB') !== false, "Must contain title");
echo "[PASS] RAAB Index View renders correctly (length: " . strlen($htmlRaab) . ")\n\n";

// Clean up test records
$item1->delete();
$item2->delete();
$item3->delete();
echo "=== ALL TESTS COMPLETED SUCCESSFULLY! ===\n";
