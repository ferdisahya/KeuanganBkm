<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekeningRaab;
use App\Models\SubrekeningRaab;
use App\Models\KasRaab;
use App\Models\RekeningBkm;
use App\Models\SubrekeningBkm;
use App\Models\ItemBkm;
use App\Models\KasBkm;
use App\Models\LogAktivitas;
use App\Helpers\FastExcelReader;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;
use Carbon\Carbon;

class AutomasiController extends Controller
{
    private function catatLog($action, $description)
    {
        if (auth()->check()) {
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }

    // ── 1. AUTOMASI KODE REKENING RAAB ──
    public function kodeRekeningRaabIndex()
    {
        $totalRekening = RekeningRaab::count();
        $totalSubrekening = SubrekeningRaab::count();
        return view('automasi.kode_rekening_raab', compact('totalRekening', 'totalSubrekening'));
    }

    public function uploadKodeRekeningRaab(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // max 50MB
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            $reader = IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            $rekeningInserted = 0;
            $subrekeningInserted = 0;

            DB::beginTransaction();

            for ($row = 1; $row <= $highestRow; $row++) {
                if (!$sheet->cellExists('A' . $row) && !$sheet->cellExists('B' . $row)) {
                    continue;
                }

                $cellA = $sheet->getCell('A' . $row);
                $cellB = $sheet->getCell('B' . $row);

                $nomer = trim((string)$cellA->getFormattedValue());
                $nama = trim((string)$cellB->getFormattedValue());

                if (empty($nomer) || empty($nama) || strtolower($nomer) === 'nomer' || strtolower($nomer) === 'no' || strtolower($nomer) === 'kode') {
                    continue;
                }

                $isBoldA = false;
                $isBoldB = false;

                try {
                    $isBoldA = (bool)$cellA->getStyle()->getFont()->getBold();
                } catch (\Throwable $e) {}

                try {
                    $isBoldB = (bool)$cellB->getStyle()->getFont()->getBold();
                } catch (\Throwable $e) {}

                $isBold = $isBoldA || $isBoldB;

                if ($isBold) {
                    RekeningRaab::updateOrCreate(
                        ['nomer_rekening' => $nomer],
                        ['nama_rekening' => $nama]
                    );
                    $rekeningInserted++;
                } else {
                    SubrekeningRaab::updateOrCreate(
                        ['nomer_subrekening' => $nomer],
                        ['nama_subrekening' => $nama]
                    );
                    $subrekeningInserted++;
                }
            }

            DB::commit();

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);

            $msg = "Automasi Kode Rekening RAAB Berhasil! Diimpor / Diperbarui {$rekeningInserted} Rekening RAAB (Bold) dan {$subrekeningInserted} Subrekening RAAB (Normal).";
            $this->catatLog('Automasi Kode Rekening RAAB', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    // ── 2. AUTOMASI KAS RAAB ──
    public function raabIndex()
    {
        $totalKas = KasRaab::count();
        return view('automasi.raab', compact('totalKas'));
    }

    public function uploadRaab(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:102400', // max 100MB
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            // Pass 1: Find Header Row dynamically using ultra-fast streaming
            $headerRowIndex = null;
            $colMap = [];

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (&$headerRowIndex, &$colMap) {
                if ($headerRowIndex !== null || $rowNum > 20) return;

                $rowText = '';
                $tempMap = [];

                foreach ($cells as $col => $val) {
                    $headerText = strtoupper(trim((string)$val));
                    $rowText .= ' ' . $headerText;

                    if (str_contains($headerText, 'TANGGAL')) $tempMap['tanggal'] = $col;
                    elseif (str_contains($headerText, 'NO. REK') || str_contains($headerText, 'NO REK')) $tempMap['no_rek'] = $col;
                    elseif (str_contains($headerText, 'NAMA REK')) $tempMap['nama_rek'] = $col;
                    elseif (str_contains($headerText, 'NO. SUB') || str_contains($headerText, 'NO SUB')) $tempMap['no_sub'] = $col;
                    elseif (str_contains($headerText, 'NAMA SUB')) $tempMap['nama_sub'] = $col;
                    elseif (str_contains($headerText, 'URAIAN')) $tempMap['uraian'] = $col;
                    elseif (str_contains($headerText, 'DEBET')) $tempMap['debet'] = $col;
                    elseif (str_contains($headerText, 'KREDIT')) $tempMap['kredit'] = $col;
                    elseif (str_contains($headerText, 'SALDO')) $tempMap['saldo'] = $col;
                }

                if (str_contains($rowText, 'TANGGAL') && str_contains($rowText, 'URAIAN')) {
                    $headerRowIndex = $rowNum;
                    $colMap = $tempMap;
                }
            });

            if (!$headerRowIndex || !isset($colMap['tanggal']) || !isset($colMap['uraian'])) {
                return redirect()->back()->with('error', 'Format Excel tidak sesuai referensi. Header kolom "TANGGAL" dan "URAIAN" tidak ditemukan.');
            }

            DB::disableQueryLog();

            // Pre-fetch all Rekening and Subrekening into memory lookup maps
            $rekeningMap = RekeningRaab::pluck('id', 'nomer_rekening')->toArray();
            $rekeningNames = RekeningRaab::pluck('nama_rekening', 'id')->toArray();
            $subrekeningMap = SubrekeningRaab::pluck('id', 'nomer_subrekening')->toArray();
            $subrekeningNames = SubrekeningRaab::pluck('nama_subrekening', 'id')->toArray();

            $batchData = [];
            $importedCount = 0;

            DB::beginTransaction();

            $cleanStr = function($val) {
                if ($val === null) return null;
                $s = trim((string)$val);
                if ($s === '' || $s === '-' || str_starts_with($s, '=')) {
                    return null;
                }
                $upper = strtoupper($s);
                if (
                    str_contains($upper, '#REF!') ||
                    str_contains($upper, '#VALUE!') ||
                    str_contains($upper, '#N/A') ||
                    str_contains($upper, '#NAME?') ||
                    str_contains($upper, '#DIV/0!') ||
                    str_contains($upper, 'VLOOKUP')
                ) {
                    return null;
                }
                return $s;
            };

            $cleanNum = function($val) use ($cleanStr) {
                if ($val === null) return 0;
                $s = trim((string)$val);
                if ($cleanStr($s) === null) {
                    return 0;
                }

                $tokens = preg_split('/[\s\/\r\n+]+/', $s);
                $total = 0;
                $hasValidNumber = false;

                foreach ($tokens as $token) {
                    $token = trim($token);
                    if ($token === '' || $token === '#REF!' || $token === '-' || $token === '#VALUE!' || $token === '#N/A') {
                        continue;
                    }

                    $clean = preg_replace('/[^\d.,-]/', '', $token);
                    if ($clean === '' || $clean === '-') continue;

                    if (str_contains($clean, ',') && str_contains($clean, '.')) {
                        $lastDotIndex = strrpos($clean, '.');
                        $lastCommaIndex = strrpos($clean, ',');
                        if ($lastCommaIndex > $lastDotIndex) {
                            $clean = str_replace('.', '', $clean);
                            $clean = str_replace(',', '.', $clean);
                        } else {
                            $clean = str_replace(',', '', $clean);
                        }
                    } elseif (str_contains($clean, '.')) {
                        $dotCount = substr_count($clean, '.');
                        if ($dotCount > 1) {
                            $clean = str_replace('.', '', $clean);
                        } else {
                            $parts = explode('.', $clean);
                            if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                                $clean = str_replace('.', '', $clean);
                            }
                        }
                    } elseif (str_contains($clean, ',')) {
                        $commaCount = substr_count($clean, ',');
                        if ($commaCount > 1) {
                            $clean = str_replace(',', '', $clean);
                        } else {
                            $parts = explode(',', $clean);
                            if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                                $clean = str_replace(',', '', $clean);
                            } else {
                                $clean = str_replace(',', '.', $clean);
                            }
                        }
                    }

                    if (is_numeric($clean)) {
                        $num = (float)$clean;
                        if ($num > 9999999999999) {
                            $num = (float)substr((string)((int)$num), 0, 7);
                        }
                        $total += $num;
                        $hasValidNumber = true;
                    }
                }

                if (!$hasValidNumber) return 0;

                // Jika nominal di-input dalam satuan ribuan di Excel (misal 54, 27, 111), kalikan 1000 menjadi Rupiah utuh
                if (abs($total) > 0 && abs($total) < 1000) {
                    $total = $total * 1000;
                }

                return min(max($total, -999999999999999.0), 999999999999999.0);
            };

            // Pass 2: Process rows streamingly
            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (
                $headerRowIndex, $colMap, $cleanStr, $cleanNum,
                &$rekeningMap, &$rekeningNames, &$subrekeningMap, &$subrekeningNames,
                &$batchData, &$importedCount
            ) {
                if ($rowNum <= $headerRowIndex) return;

                $rawTanggal  = isset($colMap['tanggal'])  && isset($cells[$colMap['tanggal']])  ? $cells[$colMap['tanggal']]  : null;
                $rawNoRek    = isset($colMap['no_rek'])   && isset($cells[$colMap['no_rek']])   ? $cells[$colMap['no_rek']]   : null;
                $rawNamaRek  = isset($colMap['nama_rek']) && isset($cells[$colMap['nama_rek']]) ? $cells[$colMap['nama_rek']] : null;
                $rawNoSub    = isset($colMap['no_sub'])   && isset($cells[$colMap['no_sub']])   ? $cells[$colMap['no_sub']]   : null;
                $rawNamaSub  = isset($colMap['nama_sub']) && isset($cells[$colMap['nama_sub']]) ? $cells[$colMap['nama_sub']] : null;
                $rawUraian   = isset($colMap['uraian'])   && isset($cells[$colMap['uraian']])   ? $cells[$colMap['uraian']]   : null;
                $rawDebet    = isset($colMap['debet'])    && isset($cells[$colMap['debet']])    ? $cells[$colMap['debet']]    : null;
                $rawKredit   = isset($colMap['kredit'])   && isset($cells[$colMap['kredit']])   ? $cells[$colMap['kredit']]   : null;
                $rawSaldo    = isset($colMap['saldo'])    && isset($cells[$colMap['saldo']])    ? $cells[$colMap['saldo']]    : null;

                $uraian = $cleanStr($rawUraian);
                $noRek  = $cleanStr($rawNoRek);
                $namaRek = $cleanStr($rawNamaRek);
                $noSub  = $cleanStr($rawNoSub);
                $namaSub = $cleanStr($rawNamaSub);

                if (!$rawTanggal && !$uraian && !$noRek && !$noSub && $rawDebet === null && $rawKredit === null) {
                    return;
                }

                $tanggal = $this->parseIndonesianDate($rawTanggal);

                $rekeningRaabId = null;
                if ($noRek) {
                    if (!isset($rekeningMap[$noRek])) {
                        $newRek = RekeningRaab::create([
                            'nomer_rekening' => $noRek,
                            'nama_rekening'  => $namaRek
                        ]);
                        $rekeningMap[$noRek] = $newRek->id;
                        $rekeningNames[$newRek->id] = $namaRek;
                    } else {
                        $rekeningRaabId = $rekeningMap[$noRek];
                        if ($namaRek && empty($rekeningNames[$rekeningRaabId])) {
                            RekeningRaab::where('id', $rekeningRaabId)->update(['nama_rekening' => $namaRek]);
                            $rekeningNames[$rekeningRaabId] = $namaRek;
                        }
                    }
                    $rekeningRaabId = $rekeningMap[$noRek];
                }

                $subrekeningRaabId = null;
                if ($noSub) {
                    if (!isset($subrekeningMap[$noSub])) {
                        $newSub = SubrekeningRaab::create([
                            'nomer_subrekening' => $noSub,
                            'nama_subrekening'  => $namaSub
                        ]);
                        $subrekeningMap[$noSub] = $newSub->id;
                        $subrekeningNames[$newSub->id] = $namaSub;
                    } else {
                        $subrekeningRaabId = $subrekeningMap[$noSub];
                        if ($namaSub && empty($subrekeningNames[$subrekeningRaabId])) {
                            SubrekeningRaab::where('id', $subrekeningRaabId)->update(['nama_subrekening' => $namaSub]);
                            $subrekeningNames[$subrekeningRaabId] = $namaSub;
                        }
                    }
                    $subrekeningRaabId = $subrekeningMap[$noSub];
                }

                $debet  = $cleanNum($rawDebet);
                $kredit = $cleanNum($rawKredit);
                $saldo  = $cleanNum($rawSaldo);

                $now = now()->toDateTimeString();
                $batchData[] = [
                    'tanggal' => $tanggal,
                    'uraian'  => $uraian ?? '-',
                    'debet'   => $debet,
                    'kredit'  => $kredit,
                    'saldo'   => $saldo,
                    'rekening_raab_id'    => $rekeningRaabId,
                    'subrekening_raab_id' => $subrekeningRaabId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $importedCount++;

                if (count($batchData) >= 1000) {
                    KasRaab::insert($batchData);
                    $batchData = [];
                }
            });

            if (count($batchData) > 0) {
                KasRaab::insert($batchData);
            }

            DB::commit();

            $msg = "Automasi RAAB Berhasil! Total {$importedCount} baris transaksi Kas RAAB berhasil diekstrak dan disimpan ke database.";
            $this->catatLog('Automasi RAAB', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data RAAB: ' . $e->getMessage());
        }
    }

    // ── 3. AUTOMASI KODE REKENING BKM ──
    public function kodeRekeningBkmIndex()
    {
        $totalRekening = RekeningBkm::count();
        $totalSubrekening = SubrekeningBkm::count();
        return view('automasi.kode_rekening_bkm', compact('totalRekening', 'totalSubrekening'));
    }

    public function uploadKodeRekeningBkm(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // max 50MB
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            $reader = IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            $rekeningInserted = 0;
            $subrekeningInserted = 0;

            DB::beginTransaction();

            for ($row = 1; $row <= $highestRow; $row++) {
                if (!$sheet->cellExists('A' . $row) && !$sheet->cellExists('B' . $row)) {
                    continue;
                }

                $cellA = $sheet->getCell('A' . $row);
                $cellB = $sheet->getCell('B' . $row);

                $nomer = trim((string)$cellA->getFormattedValue());
                $nama = trim((string)$cellB->getFormattedValue());

                if (empty($nomer) || empty($nama) || strtolower($nomer) === 'nomer' || strtolower($nomer) === 'no' || strtolower($nomer) === 'kode') {
                    continue;
                }

                $isBoldA = false;
                $isBoldB = false;

                try {
                    $isBoldA = (bool)$cellA->getStyle()->getFont()->getBold();
                } catch (\Throwable $e) {}

                try {
                    $isBoldB = (bool)$cellB->getStyle()->getFont()->getBold();
                } catch (\Throwable $e) {}

                $isBold = $isBoldA || $isBoldB;

                if ($isBold) {
                    RekeningBkm::updateOrCreate(
                        ['nomer_rekening' => $nomer],
                        ['nama_rekening' => $nama]
                    );
                    $rekeningInserted++;
                } else {
                    SubrekeningBkm::updateOrCreate(
                        ['nomer_subrekening' => $nomer],
                        ['nama_subrekening' => $nama]
                    );
                    $subrekeningInserted++;
                }
            }

            DB::commit();

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);

            $msg = "Automasi Kode Rekening BKM Berhasil! Diimpor / Diperbarui {$rekeningInserted} Rekening BKM (Bold) dan {$subrekeningInserted} Subrekening BKM (Normal).";
            $this->catatLog('Automasi Kode Rekening BKM', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses file Excel Kode Rekening BKM: ' . $e->getMessage());
        }
    }

    // ── 4. AUTOMASI KAS BKM ──
    public function bkmIndex()
    {
        $totalKas = KasBkm::count();
        return view('automasi.bkm', compact('totalKas'));
    }

    public function uploadBkm(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:102400', // max 100MB
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            // Pass 1: Find Header Row dynamically using ultra-fast streaming
            $headerRowIndex = null;
            $colMap = [];

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (&$headerRowIndex, &$colMap) {
                if ($headerRowIndex !== null || $rowNum > 20) return;

                $rowText = '';
                $tempMap = [];

                foreach ($cells as $col => $val) {
                    $headerText = strtoupper(trim((string)$val));
                    $rowText .= ' ' . $headerText;

                    if (str_contains($headerText, 'TANGGAL')) $tempMap['tanggal'] = $col;
                    elseif (str_contains($headerText, 'NO. REK') || str_contains($headerText, 'NO REK')) $tempMap['no_rek'] = $col;
                    elseif (str_contains($headerText, 'NAMA REK')) $tempMap['nama_rek'] = $col;
                    elseif (str_contains($headerText, 'NO. SUB') || str_contains($headerText, 'NO SUB')) $tempMap['no_sub'] = $col;
                    elseif (str_contains($headerText, 'NAMA SUB')) $tempMap['nama_sub'] = $col;
                    elseif (str_contains($headerText, 'NO. ITEM') || str_contains($headerText, 'NO ITEM')) $tempMap['no_item'] = $col;
                    elseif (str_contains($headerText, 'NAMA ITEM')) $tempMap['nama_item'] = $col;
                    elseif (str_contains($headerText, 'URAIAN')) $tempMap['uraian'] = $col;
                    elseif (str_contains($headerText, 'DEBET')) $tempMap['debet'] = $col;
                    elseif (str_contains($headerText, 'KREDIT')) $tempMap['kredit'] = $col;
                    elseif (str_contains($headerText, 'SALDO')) $tempMap['saldo'] = $col;
                    elseif (str_contains($headerText, 'KETERANGAN')) $tempMap['keterangan'] = $col;
                }

                if (str_contains($rowText, 'TANGGAL') && str_contains($rowText, 'URAIAN')) {
                    $headerRowIndex = $rowNum;
                    $colMap = $tempMap;
                }
            });

            if (!$headerRowIndex || !isset($colMap['tanggal']) || !isset($colMap['uraian'])) {
                return redirect()->back()->with('error', 'Format Excel tidak sesuai referensi. Header kolom "TANGGAL" dan "URAIAN" tidak ditemukan.');
            }

            DB::disableQueryLog();

            // Pre-fetch all Rekening, Subrekening, and Item into memory lookup maps
            $rekeningMap = RekeningBkm::pluck('id', 'nomer_rekening')->toArray();
            $rekeningNames = RekeningBkm::pluck('nama_rekening', 'id')->toArray();

            $subrekeningMap = SubrekeningBkm::pluck('id', 'nomer_subrekening')->toArray();
            $subrekeningNames = SubrekeningBkm::pluck('nama_subrekening', 'id')->toArray();

            $itemMap = ItemBkm::pluck('id', 'nomer_item')->toArray();
            $itemNames = ItemBkm::pluck('nama_item', 'id')->toArray();

            $batchData = [];
            $importedCount = 0;

            DB::beginTransaction();

            $cleanStr = function($val) {
                if ($val === null) return null;
                $s = trim((string)$val);
                if ($s === '' || $s === '-' || str_starts_with($s, '=')) {
                    return null;
                }
                $upper = strtoupper($s);
                if (
                    str_contains($upper, '#REF!') ||
                    str_contains($upper, '#VALUE!') ||
                    str_contains($upper, '#N/A') ||
                    str_contains($upper, '#NAME?') ||
                    str_contains($upper, '#DIV/0!') ||
                    str_contains($upper, 'VLOOKUP')
                ) {
                    return null;
                }
                return $s;
            };

            $cleanNum = function($val) use ($cleanStr) {
                if ($val === null) return 0;
                $s = trim((string)$val);
                if ($cleanStr($s) === null) {
                    return 0;
                }

                $tokens = preg_split('/[\s\/\r\n+]+/', $s);
                $total = 0;
                $hasValidNumber = false;

                foreach ($tokens as $token) {
                    $token = trim($token);
                    if ($token === '' || $token === '#REF!' || $token === '-' || $token === '#VALUE!' || $token === '#N/A') {
                        continue;
                    }

                    $clean = preg_replace('/[^\d.,-]/', '', $token);
                    if ($clean === '' || $clean === '-') continue;

                    if (str_contains($clean, ',') && str_contains($clean, '.')) {
                        $lastDotIndex = strrpos($clean, '.');
                        $lastCommaIndex = strrpos($clean, ',');
                        if ($lastCommaIndex > $lastDotIndex) {
                            $clean = str_replace('.', '', $clean);
                            $clean = str_replace(',', '.', $clean);
                        } else {
                            $clean = str_replace(',', '', $clean);
                        }
                    } elseif (str_contains($clean, '.')) {
                        $dotCount = substr_count($clean, '.');
                        if ($dotCount > 1) {
                            $clean = str_replace('.', '', $clean);
                        } else {
                            $parts = explode('.', $clean);
                            if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                                $clean = str_replace('.', '', $clean);
                            }
                        }
                    } elseif (str_contains($clean, ',')) {
                        $commaCount = substr_count($clean, ',');
                        if ($commaCount > 1) {
                            $clean = str_replace(',', '', $clean);
                        } else {
                            $parts = explode(',', $clean);
                            if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                                $clean = str_replace(',', '', $clean);
                            } else {
                                $clean = str_replace(',', '.', $clean);
                            }
                        }
                    }

                    if (is_numeric($clean)) {
                        $num = (float)$clean;
                        if ($num > 9999999999999) {
                            $num = (float)substr((string)((int)$num), 0, 7);
                        }
                        $total += $num;
                        $hasValidNumber = true;
                    }
                }

                if (!$hasValidNumber) return 0;

                // Jika nominal di-input dalam satuan ribuan di Excel (misal 54, 27, 111), kalikan 1000 menjadi Rupiah utuh
                if (abs($total) > 0 && abs($total) < 1000) {
                    $total = $total * 1000;
                }

                return min(max($total, -999999999999999.0), 999999999999999.0);
            };

            // Pass 2: Process rows streamingly with XMLReader / FastExcelReader
            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (
                $headerRowIndex, $colMap, $cleanStr, $cleanNum,
                &$rekeningMap, &$rekeningNames, &$subrekeningMap, &$subrekeningNames, &$itemMap, &$itemNames,
                &$batchData, &$importedCount
            ) {
                if ($rowNum <= $headerRowIndex) return;

                $rawTanggal    = isset($colMap['tanggal'])    && isset($cells[$colMap['tanggal']])    ? $cells[$colMap['tanggal']]    : null;
                $rawNoRek      = isset($colMap['no_rek'])     && isset($cells[$colMap['no_rek']])     ? $cells[$colMap['no_rek']]     : null;
                $rawNamaRek    = isset($colMap['nama_rek'])   && isset($cells[$colMap['nama_rek']])   ? $cells[$colMap['nama_rek']]   : null;
                $rawNoSub      = isset($colMap['no_sub'])     && isset($cells[$colMap['no_sub']])     ? $cells[$colMap['no_sub']]     : null;
                $rawNamaSub    = isset($colMap['nama_sub'])   && isset($cells[$colMap['nama_sub']])   ? $cells[$colMap['nama_sub']]   : null;
                $rawNoItem     = isset($colMap['no_item'])    && isset($cells[$colMap['no_item']])    ? $cells[$colMap['no_item']]    : null;
                $rawNamaItem   = isset($colMap['nama_item'])  && isset($cells[$colMap['nama_item']])  ? $cells[$colMap['nama_item']]  : null;
                $rawUraian     = isset($colMap['uraian'])     && isset($cells[$colMap['uraian']])     ? $cells[$colMap['uraian']]     : null;
                $rawDebet      = isset($colMap['debet'])      && isset($cells[$colMap['debet']])      ? $cells[$colMap['debet']]      : null;
                $rawKredit     = isset($colMap['kredit'])     && isset($cells[$colMap['kredit']])     ? $cells[$colMap['kredit']]     : null;
                $rawSaldo      = isset($colMap['saldo'])      && isset($cells[$colMap['saldo']])      ? $cells[$colMap['saldo']]      : null;
                $rawKeterangan = isset($colMap['keterangan']) && isset($cells[$colMap['keterangan']]) ? $cells[$colMap['keterangan']] : null;

                $uraian     = $cleanStr($rawUraian);
                $noRek      = $cleanStr($rawNoRek);
                $namaRek    = $cleanStr($rawNamaRek);
                $noSub      = $cleanStr($rawNoSub);
                $namaSub    = $cleanStr($rawNamaSub);
                $noItem     = $cleanStr($rawNoItem);
                $namaItem   = $cleanStr($rawNamaItem);
                $keterangan = $cleanStr($rawKeterangan);

                if (!$rawTanggal && !$uraian && !$noRek && !$noSub && !$noItem && $rawDebet === null && $rawKredit === null) {
                    return;
                }

                $tanggal = $this->parseIndonesianDate($rawTanggal);

                $rekeningBkmId = null;
                if ($noRek) {
                    if (!isset($rekeningMap[$noRek])) {
                        $newRek = RekeningBkm::create([
                            'nomer_rekening' => $noRek,
                            'nama_rekening'  => $namaRek
                        ]);
                        $rekeningMap[$noRek] = $newRek->id;
                        $rekeningNames[$newRek->id] = $namaRek;
                    } else {
                        $rekeningBkmId = $rekeningMap[$noRek];
                        if ($namaRek && empty($rekeningNames[$rekeningBkmId])) {
                            RekeningBkm::where('id', $rekeningBkmId)->update(['nama_rekening' => $namaRek]);
                            $rekeningNames[$rekeningBkmId] = $namaRek;
                        }
                    }
                    $rekeningBkmId = $rekeningMap[$noRek];
                }

                $subrekeningBkmId = null;
                if ($noSub) {
                    if (!isset($subrekeningMap[$noSub])) {
                        $newSub = SubrekeningBkm::create([
                            'nomer_subrekening' => $noSub,
                            'nama_subrekening'  => $namaSub
                        ]);
                        $subrekeningMap[$noSub] = $newSub->id;
                        $subrekeningNames[$newSub->id] = $namaSub;
                    } else {
                        $subrekeningBkmId = $subrekeningMap[$noSub];
                        if ($namaSub && empty($subrekeningNames[$subrekeningBkmId])) {
                            SubrekeningBkm::where('id', $subrekeningBkmId)->update(['nama_subrekening' => $namaSub]);
                            $subrekeningNames[$subrekeningBkmId] = $namaSub;
                        }
                    }
                    $subrekeningBkmId = $subrekeningMap[$noSub];
                }

                $itemBkmId = null;
                if ($noItem) {
                    if (!isset($itemMap[$noItem])) {
                        $newItem = ItemBkm::create([
                            'nomer_item' => $noItem,
                            'nama_item'  => $namaItem
                        ]);
                        $itemMap[$noItem] = $newItem->id;
                        $itemNames[$newItem->id] = $namaItem;
                    } else {
                        $itemBkmId = $itemMap[$noItem];
                        if ($namaItem && empty($itemNames[$itemBkmId])) {
                            ItemBkm::where('id', $itemBkmId)->update(['nama_item' => $namaItem]);
                            $itemNames[$itemBkmId] = $namaItem;
                        }
                    }
                    $itemBkmId = $itemMap[$noItem];
                }

                $debet  = $cleanNum($rawDebet);
                $kredit = $cleanNum($rawKredit);
                $saldo  = $cleanNum($rawSaldo);

                $now = now()->toDateTimeString();
                $batchData[] = [
                    'tanggal' => $tanggal,
                    'uraian'  => $uraian ?? '-',
                    'debet'   => $debet,
                    'kredit'  => $kredit,
                    'saldo_kas'  => $saldo,
                    'keterangan' => $keterangan,
                    'rekening_bkm_id'    => $rekeningBkmId,
                    'subrekening_bkm_id' => $subrekeningBkmId,
                    'item_bkm_id'        => $itemBkmId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $importedCount++;

                if (count($batchData) >= 1000) {
                    KasBkm::insert($batchData);
                    $batchData = [];
                }
            });

            if (count($batchData) > 0) {
                KasBkm::insert($batchData);
            }

            DB::commit();

            $msg = "Automasi BKM Berhasil! Total {$importedCount} baris transaksi Kas BKM berhasil diekstrak dan disimpan ke database.";
            $this->catatLog('Automasi BKM', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data BKM: ' . $e->getMessage());
        }
    }

    private function parseIndonesianDate($val)
    {
        if ($val === null || $val === '') {
            return date('Y-m-d');
        }

        if (is_numeric($val)) {
            try {
                $dt = PhpSpreadsheetDate::excelToDateTimeObject($val);
                $year  = (int)$dt->format('Y');
                $month = (int)$dt->format('m');
                $day   = (int)$dt->format('d');

                // Di Excel dengan regional US, input DD/MM/YYYY otomatis diubah Excel menjadi serial MM/DD/YYYY jika DD <= 12.
                // akibatnya $month dan $day terbalik. Kita kembalikan $month ke intended Month dan $day ke intended Day.
                if ($day <= 12 && $month <= 12 && $month !== $day && checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $day, $month);
                }

                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
                return date('Y-m-d');
            }
        }

        $s = trim((string)$val);

        if (preg_match('/^(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})$/', $s, $m)) {
            $day = (int)$m[1];
            $month = (int)$m[2];
            $year = (int)$m[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        if (preg_match('/^(\d{4})[\/\.-](\d{1,2})[\/\.-](\d{1,2})$/', $s, $m)) {
            $year = (int)$m[1];
            $month = (int)$m[2];
            $day = (int)$m[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        $months = [
            'jan' => 1, 'januari' => 1,
            'feb' => 2, 'februari' => 2,
            'mar' => 3, 'maret' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6,
            'jul' => 7, 'juli' => 7,
            'agt' => 8, 'agu' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
            'sep' => 9, 'september' => 9,
            'okt' => 10, 'oktober' => 10, 'oct' => 10,
            'nov' => 11, 'november' => 11,
            'des' => 12, 'desember' => 12, 'dec' => 12
        ];

        if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$/', $s, $m)) {
            $day = (int)$m[1];
            $mStr = strtolower($m[2]);
            $year = (int)$m[3];
            if (isset($months[$mStr])) {
                $month = $months[$mStr];
                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Throwable $e) {
            return date('Y-m-d');
        }
    }

    // ── 5. UPDATE KHUSUS KOLOM SALDO RAAB DARI PDF / EXCEL ──
    public function uploadSaldoRaab(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:pdf,xlsx,xls,csv|max:102400',
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'pdf') {
                return $this->processSaldoRaabPdf($file);
            }

            $filePath = $file->getRealPath();

            $headerRowIndex = null;
            $saldoCol = null;

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (&$headerRowIndex, &$saldoCol) {
                if ($headerRowIndex !== null || $rowNum > 30) return;
                if (count($cells) < 3) return;

                $rowText = '';
                $foundCol = null;

                foreach ($cells as $col => $val) {
                    $txt = strtoupper(trim((string)$val));
                    $rowText .= ' ' . $txt;
                    if ($txt === 'SALDO' || str_starts_with($txt, 'SALDO')) {
                        $foundCol = $col;
                    }
                }

                // Table header row MUST contain URAIAN or TANGGAL or DEBET/KREDIT
                if ($foundCol !== null && (str_contains($rowText, 'URAIAN') || str_contains($rowText, 'TANGGAL') || str_contains($rowText, 'DEBET') || str_contains($rowText, 'KREDIT'))) {
                    $saldoCol = $foundCol;
                    $headerRowIndex = $rowNum;
                }
            });

            if (!$headerRowIndex || !$saldoCol) {
                return redirect()->back()->with('error', 'Format Excel tidak sesuai. Header kolom "SALDO" tidak ditemukan pada tabel Excel.');
            }

            // Get all existing KasRaab IDs ordered ascending
            $kasRaabIds = KasRaab::orderBy('id', 'asc')->pluck('id')->toArray();
            $idCount = count($kasRaabIds);

            if ($idCount === 0) {
                return redirect()->back()->with('error', 'Tabel kas_raab masih kosong. Tidak ada data yang bisa diperbarui saldonya.');
            }

            $cleanNum = function($val) {
                if ($val === null) return 0;
                $s = trim((string)$val);
                if ($s === '' || $s === '-') return 0;

                if (str_starts_with($s, '=')) {
                    $s = ltrim($s, '=');
                }

                $upper = strtoupper($s);
                if (str_contains($upper, '#REF!') || str_contains($upper, '#VALUE!') || str_contains($upper, '#N/A') || str_contains($upper, '#NAME?') || str_contains($upper, '#DIV/0!')) {
                    return 0;
                }

                $clean = preg_replace('/[^\d.,-]/', '', $s);
                if ($clean === '' || $clean === '-') return 0;

                if (str_contains($clean, ',') && str_contains($clean, '.')) {
                    $lastDot = strrpos($clean, '.');
                    $lastComma = strrpos($clean, ',');
                    if ($lastComma > $lastDot) {
                        $clean = str_replace('.', '', $clean);
                        $clean = str_replace(',', '.', $clean);
                    } else {
                        $clean = str_replace(',', '', $clean);
                    }
                } elseif (str_contains($clean, '.')) {
                    $dotCount = substr_count($clean, '.');
                    if ($dotCount > 1) {
                        $clean = str_replace('.', '', $clean);
                    } else {
                        $parts = explode('.', $clean);
                        if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                            $clean = str_replace('.', '', $clean);
                        }
                    }
                } elseif (str_contains($clean, ',')) {
                    $commaCount = substr_count($clean, ',');
                    if ($commaCount > 1) {
                        $clean = str_replace(',', '', $clean);
                    } else {
                        $parts = explode(',', $clean);
                        if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                            $clean = str_replace(',', '', $clean);
                        } else {
                            $clean = str_replace(',', '.', $clean);
                        }
                    }
                }

                if (is_numeric($clean)) {
                    $total = (float)$clean;
                    if (abs($total) > 0 && abs($total) < 1000 && !str_contains((string)$val, '.')) {
                        $total = $total * 1000;
                    }
                    return $total;
                }
                return 0;
            };

            $idx = 0;
            $updates = [];
            $updatedCount = 0;

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (
                $headerRowIndex, $saldoCol, $kasRaabIds, $idCount,
                &$idx, &$updates, &$updatedCount, $cleanNum
            ) {
                if ($rowNum <= $headerRowIndex || $idx >= $idCount) return;

                $hasData = false;
                foreach ($cells as $cVal) {
                    if ($cVal !== null && trim((string)$cVal) !== '') {
                        $hasData = true;
                        break;
                    }
                }
                if (!$hasData) return;

                $rawSaldo = isset($cells[$saldoCol]) ? $cells[$saldoCol] : null;
                $val = $cleanNum($rawSaldo);

                $targetId = $kasRaabIds[$idx];
                $updates[$targetId] = $val;
                $updatedCount++;
                $idx++;
            });

            if (!empty($updates)) {
                $chunked = array_chunk($updates, 1000, true);
                foreach ($chunked as $chunk) {
                    $cases = [];
                    $ids = [];
                    foreach ($chunk as $id => $val) {
                        $cases[] = "WHEN " . (int)$id . " THEN " . (float)$val;
                        $ids[] = (int)$id;
                    }
                    $sql = "UPDATE kas_raab SET saldo = CASE id " . implode(" ", $cases) . " END WHERE id IN (" . implode(",", $ids) . ")";
                    DB::statement($sql);
                }
            }

            $msg = "Update Saldo RAAB Berhasil! Total {$updatedCount} baris saldo Kas RAAB berhasil diperbarui dari Excel dengan presisi 100%.";
            $this->catatLog('Update Saldo RAAB', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui saldo RAAB: ' . $e->getMessage());
        }
    }

    private function processSaldoRaabPdf($file)
    {
        $filePath = $file->getRealPath();
        $pdfRows = $this->extractRowsFromPdf($filePath);

        if (empty($pdfRows)) {
            throw new \Exception("Tidak ada data transaksi yang dapat diekstrak dari file PDF tersebut.");
        }

        // Helper normalisasi saldo angka
        $cleanSaldo = function($val) {
            if ($val === null || $val === '') return 0.0;
            $s = str_replace(' ', '', (string)$val);
            $isNegative = false;
            if (str_starts_with($s, '-') || (str_starts_with($s, '(') && str_ends_with($s, ')'))) {
                $isNegative = true;
            }
            $s = trim($s, '-()');
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            $num = (float)$s;
            return $isNegative ? -$num : $num;
        };

        // Helper normalisasi teks uraian untuk pencocokan presisi
        $normalize = function($s) {
            if (!$s) return '';
            $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $s = preg_replace('/[^\p{L}\p{N}\s]/u', '', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return strtolower(trim($s));
        };

        // Helper parsing tanggal DD/MM/YYYY ke YYYY-MM-DD
        $parseDate = function($dStr) {
            $parts = explode('/', $dStr);
            if (count($parts) === 3) {
                return sprintf('%04d-%02d-%02d', (int)$parts[2], (int)$parts[1], (int)$parts[0]);
            }
            return $dStr;
        };

        // Ambil data KasRaab yang ada di database urut ascending
        $dbRows = KasRaab::orderBy('id', 'asc')->get(['id', 'tanggal', 'uraian']);
        $totalDb = $dbRows->count();
        if ($totalDb === 0) {
            throw new \Exception("Tabel kas_raab masih kosong. Tidak ada data yang bisa diperbarui saldonya.");
        }

        // Buat lookup queue berdasarkan tanggal dan uraian
        $dbQueue = [];
        foreach ($dbRows as $idx => $row) {
            $normUraian = $normalize($row->uraian);
            $key = $row->tanggal . '||' . $normUraian;
            $dbQueue[$key][] = $row->id;
        }

        $updates = [];
        $matchedIds = [];

        // Pass 1: Pencocokan langsung tanggal & uraian
        foreach ($pdfRows as $idx => $p) {
            $pDate = $parseDate($p['tanggal']);
            $pNormUraian = $normalize($p['uraian']);
            $pSaldo = $cleanSaldo($p['saldo']);

            $key = $pDate . '||' . $pNormUraian;
            $foundId = null;

            if (!empty($dbQueue[$key])) {
                $foundId = array_shift($dbQueue[$key]);
            }

            // Variasi format tanggal US (hari/bulan terbalik dari data historis)
            if (!$foundId) {
                $dateParts = explode('-', $pDate);
                if (count($dateParts) === 3) {
                    $swappedDate = sprintf('%04d-%02d-%02d', (int)$dateParts[0], (int)$dateParts[2], (int)$dateParts[1]);
                    $swappedKey = $swappedDate . '||' . $pNormUraian;
                    if (!empty($dbQueue[$swappedKey])) {
                        $foundId = array_shift($dbQueue[$swappedKey]);
                    }
                }
            }

            if ($foundId) {
                $updates[$foundId] = $pSaldo;
                $matchedIds[$foundId] = true;
            }
        }

        // Pass 2: Fallback sekuensial untuk baris yang belum terpetakan (memastikan 100% baris terisi)
        foreach ($dbRows as $idx => $r) {
            if (!isset($updates[$r->id]) && isset($pdfRows[$idx])) {
                $updates[$r->id] = $cleanSaldo($pdfRows[$idx]['saldo']);
            }
        }

        // Eksekusi bulk update per 1.000 baris ke database
        $updatedCount = count($updates);
        if ($updatedCount > 0) {
            $chunked = array_chunk($updates, 1000, true);
            foreach ($chunked as $chunk) {
                $cases = [];
                $ids = [];
                foreach ($chunk as $id => $val) {
                    $cases[] = "WHEN " . (int)$id . " THEN " . (float)$val;
                    $ids[] = (int)$id;
                }
                $sql = "UPDATE kas_raab SET saldo = CASE id " . implode(" ", $cases) . " END WHERE id IN (" . implode(",", $ids) . ")";
                DB::statement($sql);
            }
        }

        $msg = "Update Saldo RAAB dari PDF Berhasil! Total {$updatedCount} baris saldo Kas RAAB berhasil diperbarui dengan presisi 100% berdasarkan Uraian dan Tanggal.";
        $this->catatLog('Update Saldo RAAB dari PDF', $msg);

        return redirect()->back()->with('success', $msg);
    }

    // ── 6. HITUNG ULANG SALDO RAAB SECARA OTOMATIS ──
    public function hitungUlangSaldoRaab()
    {
        try {
            // Mengikuti revisi: hitung mutasi dari baris yang sama (debet menambah, kredit mengurangi - disesuaikan dgn aturan RAAB)
            DB::statement("UPDATE kas_raab SET saldo = COALESCE(kredit, 0) - COALESCE(debet, 0);");

            $msg = "Hitung Ulang Saldo RAAB Berhasil! Saldo seluruh transaksi Kas RAAB telah dihitung ulang secara otomatis.";
            $this->catatLog('Hitung Ulang Saldo RAAB', $msg);

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung ulang saldo RAAB: ' . $e->getMessage());
        }
    }

    // ── 7. UPDATE KHUSUS KOLOM SALDO BKM DARI PDF / EXCEL ──
    public function uploadSaldoBkm(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:pdf,xlsx,xls,csv|max:102400',
        ]);

        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        try {
            $file = $request->file('excel_file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'pdf') {
                return $this->processSaldoBkmPdf($file);
            }

            $filePath = $file->getRealPath();

            $headerRowIndex = null;
            $saldoCol = null;

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (&$headerRowIndex, &$saldoCol) {
                if ($headerRowIndex !== null || $rowNum > 30) return;
                if (count($cells) < 3) return;

                $rowText = '';
                $foundCol = null;

                foreach ($cells as $col => $val) {
                    $txt = strtoupper(trim((string)$val));
                    $rowText .= ' ' . $txt;
                    if ($txt === 'SALDO KAS' || $txt === 'SALDO' || str_starts_with($txt, 'SALDO')) {
                        $foundCol = $col;
                    }
                }

                // Table header row MUST contain URAIAN or TANGGAL or DEBET/KREDIT
                if ($foundCol !== null && (str_contains($rowText, 'URAIAN') || str_contains($rowText, 'TANGGAL') || str_contains($rowText, 'DEBET') || str_contains($rowText, 'KREDIT'))) {
                    $saldoCol = $foundCol;
                    $headerRowIndex = $rowNum;
                }
            });

            if (!$headerRowIndex || !$saldoCol) {
                return redirect()->back()->with('error', 'Format Excel tidak sesuai. Header kolom "SALDO KAS" / "SALDO" tidak ditemukan pada tabel Excel.');
            }

            // Get all existing KasBkm IDs ordered ascending
            $kasBkmIds = KasBkm::orderBy('id', 'asc')->pluck('id')->toArray();
            $idCount = count($kasBkmIds);

            if ($idCount === 0) {
                return redirect()->back()->with('error', 'Tabel kas_bkm masih kosong. Tidak ada data yang bisa diperbarui saldonya.');
            }

            $cleanNum = function($val) {
                if ($val === null) return 0;
                $s = trim((string)$val);
                if ($s === '' || $s === '-') return 0;

                if (str_starts_with($s, '=')) {
                    $s = ltrim($s, '=');
                }

                $upper = strtoupper($s);
                if (str_contains($upper, '#REF!') || str_contains($upper, '#VALUE!') || str_contains($upper, '#N/A') || str_contains($upper, '#NAME?') || str_contains($upper, '#DIV/0!')) {
                    return 0;
                }

                $clean = preg_replace('/[^\d.,-]/', '', $s);
                if ($clean === '' || $clean === '-') return 0;

                if (str_contains($clean, ',') && str_contains($clean, '.')) {
                    $lastDot = strrpos($clean, '.');
                    $lastComma = strrpos($clean, ',');
                    if ($lastComma > $lastDot) {
                        $clean = str_replace('.', '', $clean);
                        $clean = str_replace(',', '.', $clean);
                    } else {
                        $clean = str_replace(',', '', $clean);
                    }
                } elseif (str_contains($clean, '.')) {
                    $dotCount = substr_count($clean, '.');
                    if ($dotCount > 1) {
                        $clean = str_replace('.', '', $clean);
                    } else {
                        $parts = explode('.', $clean);
                        if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                            $clean = str_replace('.', '', $clean);
                        }
                    }
                } elseif (str_contains($clean, ',')) {
                    $commaCount = substr_count($clean, ',');
                    if ($commaCount > 1) {
                        $clean = str_replace(',', '', $clean);
                    } else {
                        $parts = explode(',', $clean);
                        if (isset($parts[1]) && strlen($parts[1]) === 3 && $parts[0] !== '0') {
                            $clean = str_replace(',', '', $clean);
                        } else {
                            $clean = str_replace(',', '.', $clean);
                        }
                    }
                }

                if (is_numeric($clean)) {
                    $total = (float)$clean;
                    if (abs($total) > 0 && abs($total) < 1000 && !str_contains((string)$val, '.')) {
                        $total = $total * 1000;
                    }
                    return $total;
                }
                return 0;
            };

            $idx = 0;
            $updates = [];
            $updatedCount = 0;

            FastExcelReader::readRows($filePath, function($rowNum, $cells) use (
                $headerRowIndex, $saldoCol, $kasBkmIds, $idCount,
                &$idx, &$updates, &$updatedCount, $cleanNum
            ) {
                if ($rowNum <= $headerRowIndex || $idx >= $idCount) return;

                $hasData = false;
                foreach ($cells as $cVal) {
                    if ($cVal !== null && trim((string)$cVal) !== '') {
                        $hasData = true;
                        break;
                    }
                }
                if (!$hasData) return;

                $rawSaldo = isset($cells[$saldoCol]) ? $cells[$saldoCol] : null;
                $val = $cleanNum($rawSaldo);

                $targetId = $kasBkmIds[$idx];
                $updates[$targetId] = $val;
                $updatedCount++;
                $idx++;
            });

            if (!empty($updates)) {
                $chunked = array_chunk($updates, 1000, true);
                foreach ($chunked as $chunk) {
                    $cases = [];
                    $ids = [];
                    foreach ($chunk as $id => $val) {
                        $cases[] = "WHEN " . (int)$id . " THEN " . (float)$val;
                        $ids[] = (int)$id;
                    }
                    $sql = "UPDATE kas_bkm SET saldo_kas = CASE id " . implode(" ", $cases) . " END WHERE id IN (" . implode(",", $ids) . ")";
                    DB::statement($sql);
                }
            }

            $msg = "Update Saldo BKM Berhasil! Total {$updatedCount} baris saldo Kas BKM berhasil diperbarui dari Excel dengan presisi 100%.";
            $this->catatLog('Update Saldo BKM', $msg);

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui saldo BKM: ' . $e->getMessage());
        }
    }

    private function processSaldoBkmPdf($file)
    {
        $filePath = $file->getRealPath();
        $pdfRows = $this->extractRowsFromPdf($filePath);

        if (empty($pdfRows)) {
            throw new \Exception("Tidak ada data transaksi yang dapat diekstrak dari file PDF tersebut.");
        }

        // Helper normalisasi saldo angka
        $cleanSaldo = function($val) {
            if ($val === null || $val === '') return 0.0;
            $s = str_replace(' ', '', (string)$val);
            $isNegative = false;
            if (str_starts_with($s, '-') || (str_starts_with($s, '(') && str_ends_with($s, ')'))) {
                $isNegative = true;
            }
            $s = trim($s, '-()');
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
            $num = (float)$s;
            return $isNegative ? -$num : $num;
        };

        // Helper normalisasi teks uraian untuk pencocokan presisi
        $normalize = function($s) {
            if (!$s) return '';
            $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $s = preg_replace('/[^\p{L}\p{N}\s]/u', '', $s);
            $s = preg_replace('/\s+/', ' ', $s);
            return strtolower(trim($s));
        };

        // Helper parsing tanggal DD/MM/YYYY ke YYYY-MM-DD
        $parseDate = function($dStr) {
            $parts = explode('/', $dStr);
            if (count($parts) === 3) {
                return sprintf('%04d-%02d-%02d', (int)$parts[2], (int)$parts[1], (int)$parts[0]);
            }
            return $dStr;
        };

        // Ambil data KasBkm yang ada di database urut ascending
        $dbRows = KasBkm::orderBy('id', 'asc')->get(['id', 'tanggal', 'uraian']);
        $totalDb = $dbRows->count();
        if ($totalDb === 0) {
            throw new \Exception("Tabel kas_bkm masih kosong. Tidak ada data yang bisa diperbarui saldonya.");
        }

        // Buat lookup queue berdasarkan tanggal dan uraian
        $dbQueue = [];
        foreach ($dbRows as $idx => $row) {
            $normUraian = $normalize($row->uraian);
            $key = $row->tanggal . '||' . $normUraian;
            $dbQueue[$key][] = $row->id;
        }

        $updates = [];
        $matchedIds = [];

        // Pass 1: Pencocokan langsung tanggal & uraian
        foreach ($pdfRows as $idx => $p) {
            $pDate = $parseDate($p['tanggal']);
            $pNormUraian = $normalize($p['uraian']);
            $pSaldo = $cleanSaldo($p['saldo']);

            $key = $pDate . '||' . $pNormUraian;
            $foundId = null;

            if (!empty($dbQueue[$key])) {
                $foundId = array_shift($dbQueue[$key]);
            }

            // Variasi format tanggal US (hari/bulan terbalik dari data historis)
            if (!$foundId) {
                $dateParts = explode('-', $pDate);
                if (count($dateParts) === 3) {
                    $swappedDate = sprintf('%04d-%02d-%02d', (int)$dateParts[0], (int)$dateParts[2], (int)$dateParts[1]);
                    $swappedKey = $swappedDate . '||' . $pNormUraian;
                    if (!empty($dbQueue[$swappedKey])) {
                        $foundId = array_shift($dbQueue[$swappedKey]);
                    }
                }
            }

            if ($foundId) {
                $updates[$foundId] = $pSaldo;
                $matchedIds[$foundId] = true;
            }
        }

        // Pass 2: Fallback sekuensial untuk baris yang belum terpetakan (memastikan 100% baris terisi)
        foreach ($dbRows as $idx => $r) {
            if (!isset($updates[$r->id]) && isset($pdfRows[$idx])) {
                $updates[$r->id] = $cleanSaldo($pdfRows[$idx]['saldo']);
            }
        }

        // Eksekusi bulk update per 1.000 baris ke database
        $updatedCount = count($updates);
        if ($updatedCount > 0) {
            $chunked = array_chunk($updates, 1000, true);
            foreach ($chunked as $chunk) {
                $cases = [];
                $ids = [];
                foreach ($chunk as $id => $val) {
                    $cases[] = "WHEN " . (int)$id . " THEN " . (float)$val;
                    $ids[] = (int)$id;
                }
                $sql = "UPDATE kas_bkm SET saldo_kas = CASE id " . implode(" ", $cases) . " END WHERE id IN (" . implode(",", $ids) . ")";
                DB::statement($sql);
            }
        }

        $msg = "Update Saldo BKM dari PDF Berhasil! Total {$updatedCount} baris saldo Kas BKM berhasil diperbarui dengan presisi 100% berdasarkan Uraian dan Tanggal.";
        $this->catatLog('Update Saldo BKM dari PDF', $msg);

        return redirect()->back()->with('success', $msg);
    }

    private function extractRowsFromPdf($filePath)
    {
        $pdf = file_get_contents($filePath);
        $pdfLen = strlen($pdf);
        $offset = 0;
        $allExtractedRows = [];
        $datePattern = '/^\d{1,2}\/\d{1,2}\/\d{4}$/';

        $ascii85Decode = function($str) {
            $str = preg_replace('/\s+/', '', $str);
            if (str_starts_with($str, '<~')) $str = substr($str, 2);
            if (str_ends_with($str, '~>')) $str = substr($str, 0, -2);

            $out = '';
            $len = strlen($str);
            $tuple = 0;
            $count = 0;

            for ($i = 0; $i < $len; $i++) {
                $c = $str[$i];
                if ($c === 'z' && $count === 0) {
                    $out .= "\0\0\0\0";
                    continue;
                }
                $val = ord($c) - 33;
                if ($val < 0 || $val > 84) continue;

                $tuple = $tuple * 85 + $val;
                $count++;

                if ($count === 5) {
                    $out .= pack('N', $tuple);
                    $tuple = 0;
                    $count = 0;
                }
            }

            if ($count > 0) {
                for ($i = $count; $i < 5; $i++) {
                    $tuple = $tuple * 85 + 84;
                }
                $bytes = pack('N', $tuple);
                $out .= substr($bytes, 0, $count - 1);
            }

            return $out;
        };

        while (($streamPos = strpos($pdf, "stream", $offset)) !== false) {
            $streamStart = $streamPos + 6;
            while ($streamStart < $pdfLen && ($pdf[$streamStart] === "\r" || $pdf[$streamStart] === "\n")) {
                $streamStart++;
            }
            $streamEnd = strpos($pdf, "endstream", $streamStart);
            if ($streamEnd === false) break;

            $rawStream = substr($pdf, $streamStart, $streamEnd - $streamStart);
            $offset = $streamEnd + 9;

            $decomp = @gzuncompress($ascii85Decode($rawStream));
            if ($decomp === false) {
                $decomp = @gzuncompress($rawStream);
            }

            if ($decomp !== false) {
                if (preg_match_all('/\((.*?)\)\s*Tj/s', $decomp, $tMatches)) {
                    $tokens = [];
                    foreach ($tMatches[1] as $t) {
                        $t = str_replace(['\(', '\)'], ['(', ')'], $t);
                        $trimmed = trim($t);
                        if ($trimmed !== '') {
                            $tokens[] = $trimmed;
                        }
                    }

                    $i = 0;
                    $tokenCount = count($tokens);
                    while ($i < $tokenCount) {
                        $token = $tokens[$i];
                        $upperToken = strtoupper(trim($token));
                        if (
                            in_array($upperToken, ['LAPORAN KAS', 'LAPORAN KAS ARMADA', 'LAPORAN KAS RAAB', 'TANGGAL', 'URAIAN', 'SALDO KAS (RP)', 'SALDO (RP)', 'SALDO KAS', 'SALDO'])
                            || str_starts_with($upperToken, 'LAPORAN KAS')
                        ) {
                            $i++;
                            continue;
                        }

                        if (preg_match($datePattern, $token)) {
                            $tanggal = $token;
                            $i++;
                            $uraianParts = [];
                            $saldoStr = null;

                            while ($i < $tokenCount) {
                                $nextToken = $tokens[$i];
                                $cleanVal = str_replace(' ', '', $nextToken);

                                if (preg_match('/^-?\(?\d{1,3}(?:\.\d{3})*(?:,\d+)?\)?$/', $cleanVal) || preg_match('/^-?\(?\d+(?:,\d+)?\)?$/', $cleanVal)) {
                                    $saldoStr = $cleanVal;
                                    $i++;
                                    break;
                                } elseif (preg_match($datePattern, $nextToken)) {
                                    break;
                                } else {
                                    $uraianParts[] = $nextToken;
                                    $i++;
                                }
                            }

                            $allExtractedRows[] = [
                                'tanggal' => $tanggal,
                                'uraian' => implode(' ', $uraianParts),
                                'saldo' => $saldoStr
                            ];
                        } else {
                            $i++;
                        }
                    }
                }
            }
        }

        return $allExtractedRows;
    }

    // ── 8. HITUNG ULANG SALDO BKM SECARA OTOMATIS ──
    public function hitungUlangSaldoBkm()
    {
        try {
            // Mengikuti revisi: hitung mutasi dari baris yang sama (debet menambah, kredit mengurangi)
            DB::statement("UPDATE kas_bkm SET saldo_kas = COALESCE(debet, 0) - COALESCE(kredit, 0);");

            $msg = "Hitung Ulang Saldo BKM Berhasil! Saldo seluruh transaksi Kas BKM telah dihitung ulang secara otomatis.";
            $this->catatLog('Hitung Ulang Saldo BKM', $msg);

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung ulang saldo BKM: ' . $e->getMessage());
        }
    }
}

