<?php

namespace App\Http\Controllers;

use App\Models\KasBkm;
use App\Models\KasRaab;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    private function applyFilter($query, Request $request)
    {
        $filterType = $request->input('filter_type', 'bulan');

        if ($filterType === 'bulan') {
            $bulan = $request->input('bulan', now()->month);
            $tahun = $request->input('tahun', now()->year);
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        } elseif ($filterType === 'tahun') {
            $tahun = $request->input('tahun', now()->year);
            $query->whereYear('tanggal', $tahun);
        } elseif ($filterType === 'range') {
            if ($request->filled('tgl_mulai')) {
                $query->whereDate('tanggal', '>=', $request->tgl_mulai);
            }
            if ($request->filled('tgl_selesai')) {
                $query->whereDate('tanggal', '<=', $request->tgl_selesai);
            }
        }

        return $query;
    }

    public function laporanBkm(Request $request)
    {
        $query = KasBkm::with(['rekeningBkm', 'subrekeningBkm', 'itemBkm'])->orderBy('tanggal', 'asc');
        $query = $this->applyFilter($query, $request);
        $transaksis = $query->get();

        $totalDebet = $transaksis->sum('debet') ?? 0;
        $totalKredit = $transaksis->sum('kredit') ?? 0;
        $saldoAkhir = $totalDebet - $totalKredit;

        // Export PDF
        if ($request->input('export') === 'pdf') {
            return view('laporan.cetak_bkm_pdf', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir'));
        }

        // Export Excel (.xlsx) using PhpSpreadsheet
        if ($request->input('export') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Kas BKM');

            // Header Title
            $sheet->setCellValue('A1', 'LAPORAN KAS BKM PT BKM');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7A0F1D'));
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Table Headers
            $headers = ['No', 'Tanggal', 'Uraian', 'No Rekening', 'Nama Rekening', 'Subrekening', 'Item BKM', 'Debet (Rp)', 'Kredit (Rp)', 'Keterangan'];
            $sheet->fromArray($headers, NULL, 'A3');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7A0F1D']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A3:J3')->applyFromArray($headerStyle);
            $sheet->getRowDimension(3)->setRowHeight(25);

            $rowIdx = 4;
            foreach ($transaksis as $index => $row) {
                $sheet->setCellValue('A' . $rowIdx, $index + 1);
                $sheet->setCellValue('B' . $rowIdx, $row->tanggal);
                $sheet->setCellValue('C' . $rowIdx, $row->uraian);
                $sheet->setCellValue('D' . $rowIdx, $row->rekeningBkm->nomer_rekening ?? '-');
                $sheet->setCellValue('E' . $rowIdx, $row->rekeningBkm->nama_rekening ?? '-');
                $sheet->setCellValue('F' . $rowIdx, $row->subrekeningBkm->nama_subrekening ?? '-');
                $sheet->setCellValue('G' . $rowIdx, $row->itemBkm->nama_item ?? '-');
                $sheet->setCellValue('H' . $rowIdx, $row->debet ?? 0);
                $sheet->setCellValue('I' . $rowIdx, $row->kredit ?? 0);
                $sheet->setCellValue('J' . $rowIdx, $row->keterangan ?? '-');

                // Alignments & Number formatting
                $sheet->getStyle('A' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('I' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getStyle('A' . $rowIdx . ':J' . $rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rowIdx++;
            }

            // Summary Rows
            $sheet->setCellValue('G' . ($rowIdx + 1), 'TOTAL DEBET');
            $sheet->setCellValue('H' . ($rowIdx + 1), $totalDebet);
            $sheet->getStyle('G' . ($rowIdx + 1) . ':H' . ($rowIdx + 1))->getFont()->setBold(true);
            $sheet->getStyle('H' . ($rowIdx + 1))->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('G' . ($rowIdx + 2), 'TOTAL KREDIT');
            $sheet->setCellValue('I' . ($rowIdx + 2), $totalKredit);
            $sheet->getStyle('G' . ($rowIdx + 2) . ':I' . ($rowIdx + 2))->getFont()->setBold(true);
            $sheet->getStyle('I' . ($rowIdx + 2))->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('G' . ($rowIdx + 3), 'SALDO AKHIR');
            $sheet->setCellValue('H' . ($rowIdx + 3), $saldoAkhir);
            $sheet->getStyle('G' . ($rowIdx + 3) . ':H' . ($rowIdx + 3))->getFont()->setBold(true);
            $sheet->getStyle('H' . ($rowIdx + 3))->getNumberFormat()->setFormatCode('#,##0');

            // Auto Column Width
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = "Laporan_Kas_BKM_" . date('Ymd_His') . ".xlsx";
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $tahunList = KasBkm::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun')->toArray();
        if (empty($tahunList)) { $tahunList = [now()->year]; }

        return view('laporan.bkm', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir', 'tahunList'));
    }

    public function laporanRaab(Request $request)
    {
        $query = KasRaab::with(['rekeningRaab', 'subrekeningRaab'])->orderBy('tanggal', 'asc');
        $query = $this->applyFilter($query, $request);
        $transaksis = $query->get();

        $totalDebet = $transaksis->sum('debet') ?? 0;
        $totalKredit = $transaksis->sum('kredit') ?? 0;
        $saldoAkhir = $totalDebet - $totalKredit;

        // Export PDF
        if ($request->input('export') === 'pdf') {
            return view('laporan.cetak_raab_pdf', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir'));
        }

        // Export Excel (.xlsx) using PhpSpreadsheet
        if ($request->input('export') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Kas RAAB');

            // Header Title
            $sheet->setCellValue('A1', 'LAPORAN KAS RAAB PT BKM');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Table Headers
            $headers = ['No', 'Tanggal', 'Uraian', 'No Rekening RAAB', 'Nama Rekening RAAB', 'Subrekening RAAB', 'Debet (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'];
            $sheet->fromArray($headers, NULL, 'A3');

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A8A']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A3:I3')->applyFromArray($headerStyle);
            $sheet->getRowDimension(3)->setRowHeight(25);

            $rowIdx = 4;
            foreach ($transaksis as $index => $row) {
                $sheet->setCellValue('A' . $rowIdx, $index + 1);
                $sheet->setCellValue('B' . $rowIdx, $row->tanggal);
                $sheet->setCellValue('C' . $rowIdx, $row->uraian);
                $sheet->setCellValue('D' . $rowIdx, $row->rekeningRaab->nomer_rekening ?? '-');
                $sheet->setCellValue('E' . $rowIdx, $row->rekeningRaab->nama_rekening ?? '-');
                $sheet->setCellValue('F' . $rowIdx, $row->subrekeningRaab->nama_subrekening ?? '-');
                $sheet->setCellValue('G' . $rowIdx, $row->debet ?? 0);
                $sheet->setCellValue('H' . $rowIdx, $row->kredit ?? 0);
                $sheet->setCellValue('I' . $rowIdx, $row->saldo ?? 0);

                // Alignments & Number formatting
                $sheet->getStyle('A' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('H' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('I' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getStyle('A' . $rowIdx . ':I' . $rowIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rowIdx++;
            }

            // Summary Rows
            $sheet->setCellValue('F' . ($rowIdx + 1), 'TOTAL DEBET');
            $sheet->setCellValue('G' . ($rowIdx + 1), $totalDebet);
            $sheet->getStyle('F' . ($rowIdx + 1) . ':G' . ($rowIdx + 1))->getFont()->setBold(true);
            $sheet->getStyle('G' . ($rowIdx + 1))->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('F' . ($rowIdx + 2), 'TOTAL KREDIT');
            $sheet->setCellValue('H' . ($rowIdx + 2), $totalKredit);
            $sheet->getStyle('F' . ($rowIdx + 2) . ':H' . ($rowIdx + 2))->getFont()->setBold(true);
            $sheet->getStyle('H' . ($rowIdx + 2))->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('F' . ($rowIdx + 3), 'SALDO AKHIR');
            $sheet->setCellValue('I' . ($rowIdx + 3), $saldoAkhir);
            $sheet->getStyle('F' . ($rowIdx + 3) . ':I' . ($rowIdx + 3))->getFont()->setBold(true);
            $sheet->getStyle('I' . ($rowIdx + 3))->getNumberFormat()->setFormatCode('#,##0');

            // Auto Column Width
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = "Laporan_Kas_RAAB_" . date('Ymd_His') . ".xlsx";
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $tahunList = KasRaab::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun')->toArray();
        if (empty($tahunList)) { $tahunList = [now()->year]; }

        return view('laporan.raab', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir', 'tahunList'));
    }

    public function tahunan(Request $request)
    {
        $tahun = (int)$request->input('tahun', now()->year);

        $tahunListBkm = KasBkm::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun')->toArray();
        $tahunListRaab = KasRaab::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun')->toArray();
        $tahunList = array_values(array_unique(array_merge($tahunListBkm, $tahunListRaab)));
        rsort($tahunList);
        if (empty($tahunList)) { $tahunList = [now()->year]; }

        $totalDebetBkm = KasBkm::whereYear('tanggal', $tahun)->sum('debet') ?? 0;
        $totalKreditBkm = KasBkm::whereYear('tanggal', $tahun)->sum('kredit') ?? 0;
        $totalDebetRaab = KasRaab::whereYear('tanggal', $tahun)->sum('debet') ?? 0;
        $totalKreditRaab = KasRaab::whereYear('tanggal', $tahun)->sum('kredit') ?? 0;

        $grandMasuk = $totalDebetBkm + $totalDebetRaab;
        $grandKeluar = $totalKreditBkm + $totalKreditRaab;
        $saldoAkhir = $grandMasuk - $grandKeluar;

        if ($request->input('export') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Tahunan');

            $sheet->setCellValue('A1', "LAPORAN TAHUNAN KAS PT BKM - TAHUN $tahun");
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7A0F1D'));

            $headers = ['Kategori / Uraian', 'Debet / Masuk (Rp)', 'Kredit / Keluar (Rp)'];
            $sheet->fromArray($headers, NULL, 'A3');
            $sheet->getStyle('A3:C3')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle('A3:C3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('7A0F1D');
            $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rows = [
                ['Kas BKM', $totalDebetBkm, $totalKreditBkm],
                ['Kas RAAB', $totalDebetRaab, $totalKreditRaab],
                ['TOTAL / SUMMARY TAHUNAN', $grandMasuk, $grandKeluar],
                ['SALDO AKHIR TAHUNAN', $saldoAkhir, '-'],
            ];

            $rIdx = 4;
            foreach ($rows as $r) {
                $sheet->setCellValue('A' . $rIdx, $r[0]);
                $sheet->setCellValue('B' . $rIdx, $r[1]);
                $sheet->setCellValue('C' . $rIdx, $r[2]);

                $sheet->getStyle('B' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                if (is_numeric($r[2])) {
                    $sheet->getStyle('C' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                }
                if ($rIdx >= 6) {
                    $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getFont()->setBold(true);
                }
                $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rIdx++;
            }

            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = "Laporan_Tahunan_Kas_" . $tahun . "_" . date('Ymd_His') . ".xlsx";
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        }

        return view('laporan.tahunan', compact(
            'tahun', 'tahunList', 'grandMasuk', 'grandKeluar', 'saldoAkhir',
            'totalDebetBkm', 'totalKreditBkm', 'totalDebetRaab', 'totalKreditRaab'
        ));
    }

    public function bulanan(Request $request)
    {
        $bulan = (int)$request->input('bulan', now()->month);
        $tahun = (int)$request->input('tahun', now()->year);

        $tahunList = KasBkm::selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun')->toArray();
        if (empty($tahunList)) { $tahunList = [now()->year]; }

        $totalDebetBkm = KasBkm::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->sum('debet') ?? 0;
        $totalKreditBkm = KasBkm::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->sum('kredit') ?? 0;
        $totalDebetRaab = KasRaab::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->sum('debet') ?? 0;
        $totalKreditRaab = KasRaab::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->sum('kredit') ?? 0;

        $grandMasuk = $totalDebetBkm + $totalDebetRaab;
        $grandKeluar = $totalKreditBkm + $totalKreditRaab;
        $saldoAkhir = $grandMasuk - $grandKeluar;

        if ($request->input('export') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Bulanan');

            $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
            $sheet->setCellValue('A1', "LAPORAN BULANAN KAS PT BKM - PERIODE " . strtoupper($namaBulan) . " $tahun");
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7A0F1D'));

            $headers = ['Kategori / Uraian', 'Debet / Masuk (Rp)', 'Kredit / Keluar (Rp)'];
            $sheet->fromArray($headers, NULL, 'A3');
            $sheet->getStyle('A3:C3')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle('A3:C3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('7A0F1D');
            $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rows = [
                ['Kas BKM', $totalDebetBkm, $totalKreditBkm],
                ['Kas RAAB', $totalDebetRaab, $totalKreditRaab],
                ['TOTAL / SUMMARY BULANAN', $grandMasuk, $grandKeluar],
                ['SALDO AKHIR BULANAN', $saldoAkhir, '-'],
            ];

            $rIdx = 4;
            foreach ($rows as $r) {
                $sheet->setCellValue('A' . $rIdx, $r[0]);
                $sheet->setCellValue('B' . $rIdx, $r[1]);
                $sheet->setCellValue('C' . $rIdx, $r[2]);

                $sheet->getStyle('B' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                if (is_numeric($r[2])) {
                    $sheet->getStyle('C' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                }
                if ($rIdx >= 6) {
                    $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getFont()->setBold(true);
                }
                $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rIdx++;
            }

            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = "Laporan_Bulanan_Kas_" . $tahun . "_" . $bulan . "_" . date('Ymd_His') . ".xlsx";
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        }

        return view('laporan.bulanan', compact(
            'bulan', 'tahun', 'tahunList', 'grandMasuk', 'grandKeluar', 'saldoAkhir',
            'totalDebetBkm', 'totalKreditBkm', 'totalDebetRaab', 'totalKreditRaab'
        ));
    }

    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $totalDebetBkm = KasBkm::whereDate('tanggal', $tanggal)->sum('debet') ?? 0;
        $totalKreditBkm = KasBkm::whereDate('tanggal', $tanggal)->sum('kredit') ?? 0;
        $totalDebetRaab = KasRaab::whereDate('tanggal', $tanggal)->sum('debet') ?? 0;
        $totalKreditRaab = KasRaab::whereDate('tanggal', $tanggal)->sum('kredit') ?? 0;

        $grandMasuk = $totalDebetBkm + $totalDebetRaab;
        $grandKeluar = $totalKreditBkm + $totalKreditRaab;
        $saldoAkhir = $grandMasuk - $grandKeluar;

        if ($request->input('export') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Harian');

            $tglFmt = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
            $sheet->setCellValue('A1', "LAPORAN HARIAN KAS PT BKM - TANGGAL $tglFmt");
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7A0F1D'));

            $headers = ['Kategori / Uraian', 'Debet / Masuk (Rp)', 'Kredit / Keluar (Rp)'];
            $sheet->fromArray($headers, NULL, 'A3');
            $sheet->getStyle('A3:C3')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle('A3:C3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('7A0F1D');
            $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rows = [
                ['Kas BKM', $totalDebetBkm, $totalKreditBkm],
                ['Kas RAAB', $totalDebetRaab, $totalKreditRaab],
                ['TOTAL / SUMMARY HARIAN', $grandMasuk, $grandKeluar],
                ['SALDO AKHIR HARIAN', $saldoAkhir, '-'],
            ];

            $rIdx = 4;
            foreach ($rows as $r) {
                $sheet->setCellValue('A' . $rIdx, $r[0]);
                $sheet->setCellValue('B' . $rIdx, $r[1]);
                $sheet->setCellValue('C' . $rIdx, $r[2]);

                $sheet->getStyle('B' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                if (is_numeric($r[2])) {
                    $sheet->getStyle('C' . $rIdx)->getNumberFormat()->setFormatCode('#,##0');
                }
                if ($rIdx >= 6) {
                    $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getFont()->setBold(true);
                }
                $sheet->getStyle('A' . $rIdx . ':C' . $rIdx)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $rIdx++;
            }

            foreach (range('A', 'C') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = "Laporan_Harian_Kas_" . $tanggal . "_" . date('Ymd_His') . ".xlsx";
            return response()->streamDownload(function() use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        }

        return view('laporan.harian', compact(
            'tanggal', 'grandMasuk', 'grandKeluar', 'saldoAkhir',
            'totalDebetBkm', 'totalKreditBkm', 'totalDebetRaab', 'totalKreditRaab'
        ));
    }
}
