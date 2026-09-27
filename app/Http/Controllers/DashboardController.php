<?php

namespace App\Http\Controllers;

use App\Models\KasBkm;
use App\Models\KasRaab;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        // Get available years from DB
        $yearsBkm = KasBkm::selectRaw('YEAR(tanggal) as y')->whereNotNull('tanggal')->distinct()->pluck('y')->toArray();
        $yearsRaab = KasRaab::selectRaw('YEAR(tanggal) as y')->whereNotNull('tanggal')->distinct()->pluck('y')->toArray();
        $availableYears = array_values(array_filter(array_unique(array_merge($yearsBkm, $yearsRaab))));
        rsort($availableYears);

        if (empty($availableYears)) {
            $availableYears = [(int) $now->year];
        }
        
        $filterMode = $request->get('filter_mode', 'semua'); // 'semua', 'tahun', 'bulan', '6_bulan'
        $selectedBulan = (int) ($request->get('bulan', $now->month));
        $selectedTahun = (int) ($request->get('tahun', in_array((int)$now->year, $availableYears) ? $now->year : $availableYears[0]));

        $chartLabels = [];
        $chartMasuk  = [];
        $chartKeluar = [];
        $namaBulan   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $namaBulanFull = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        $totalDebetBkm  = 0;
        $totalKreditBkm = 0;
        $totalDebetRaab  = 0;
        $totalKreditRaab = 0;

        if ($filterMode === 'semua') {
            // Mode Semua Waktu (Default) -> Breakdown per Tahun di Database
            $distinctYears = $availableYears;
            sort($distinctYears);

            $bkmGrouped = KasBkm::selectRaw('YEAR(tanggal) as yr, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->whereNotNull('tanggal')
                ->groupBy('yr')
                ->get()
                ->keyBy('yr');

            $raabGrouped = KasRaab::selectRaw('YEAR(tanggal) as yr, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->whereNotNull('tanggal')
                ->groupBy('yr')
                ->get()
                ->keyBy('yr');

            foreach ($distinctYears as $yr) {
                $chartLabels[] = 'Tahun ' . $yr;

                $dBkm  = (float) ($bkmGrouped[$yr]->total_debet ?? 0);
                $kBkm  = (float) ($bkmGrouped[$yr]->total_kredit ?? 0);
                $dRaab = (float) ($raabGrouped[$yr]->total_debet ?? 0);
                $kRaab = (float) ($raabGrouped[$yr]->total_kredit ?? 0);

                $chartMasuk[]  = $dBkm + $dRaab;
                $chartKeluar[] = $kBkm + $kRaab;
            }

            $totalDebetBkm   = (float) $bkmGrouped->sum('total_debet');
            $totalKreditBkm  = (float) $bkmGrouped->sum('total_kredit');
            $totalDebetRaab  = (float) $raabGrouped->sum('total_debet');
            $totalKreditRaab = (float) $raabGrouped->sum('total_kredit');

            $periodeLabel = 'Semua Waktu';

        } elseif ($filterMode === 'bulan') {
            // Mode Filter Bulan -> Breakdown per Hari dalam Bulan & Tahun tersebut
            $daysInMonth = Carbon::createFromDate($selectedTahun, $selectedBulan, 1)->daysInMonth;

            $bkmGrouped = KasBkm::whereMonth('tanggal', $selectedBulan)
                ->whereYear('tanggal', $selectedTahun)
                ->selectRaw('DAY(tanggal) as d, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->groupBy('d')
                ->get()
                ->keyBy('d');

            $raabGrouped = KasRaab::whereMonth('tanggal', $selectedBulan)
                ->whereYear('tanggal', $selectedTahun)
                ->selectRaw('DAY(tanggal) as d, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->groupBy('d')
                ->get()
                ->keyBy('d');

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $chartLabels[] = $d . ' ' . $namaBulan[$selectedBulan - 1];

                $dBkm  = (float) ($bkmGrouped[$d]->total_debet ?? 0);
                $kBkm  = (float) ($bkmGrouped[$d]->total_kredit ?? 0);
                $dRaab = (float) ($raabGrouped[$d]->total_debet ?? 0);
                $kRaab = (float) ($raabGrouped[$d]->total_kredit ?? 0);

                $chartMasuk[]  = $dBkm + $dRaab;
                $chartKeluar[] = $kBkm + $kRaab;
            }

            $totalDebetBkm   = (float) $bkmGrouped->sum('total_debet');
            $totalKreditBkm  = (float) $bkmGrouped->sum('total_kredit');
            $totalDebetRaab  = (float) $raabGrouped->sum('total_debet');
            $totalKreditRaab = (float) $raabGrouped->sum('total_kredit');

            $periodeLabel = $namaBulanFull[$selectedBulan - 1] . ' ' . $selectedTahun;

        } elseif ($filterMode === 'tahun') {
            // Mode Filter Tahun -> Breakdown 12 Bulan (Jan - Des)
            $bkmGrouped = KasBkm::whereYear('tanggal', $selectedTahun)
                ->selectRaw('MONTH(tanggal) as m, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->groupBy('m')
                ->get()
                ->keyBy('m');

            $raabGrouped = KasRaab::whereYear('tanggal', $selectedTahun)
                ->selectRaw('MONTH(tanggal) as m, SUM(debet) as total_debet, SUM(kredit) as total_kredit')
                ->groupBy('m')
                ->get()
                ->keyBy('m');

            for ($m = 1; $m <= 12; $m++) {
                $chartLabels[] = $namaBulan[$m - 1] . ' ' . $selectedTahun;

                $dBkm  = (float) ($bkmGrouped[$m]->total_debet ?? 0);
                $kBkm  = (float) ($bkmGrouped[$m]->total_kredit ?? 0);
                $dRaab = (float) ($raabGrouped[$m]->total_debet ?? 0);
                $kRaab = (float) ($raabGrouped[$m]->total_kredit ?? 0);

                $chartMasuk[]  = $dBkm + $dRaab;
                $chartKeluar[] = $kBkm + $kRaab;
            }

            $totalDebetBkm   = (float) $bkmGrouped->sum('total_debet');
            $totalKreditBkm  = (float) $bkmGrouped->sum('total_kredit');
            $totalDebetRaab  = (float) $raabGrouped->sum('total_debet');
            $totalKreditRaab = (float) $raabGrouped->sum('total_kredit');

            $periodeLabel = 'Tahun ' . $selectedTahun;

        } else {
            // Mode 6 Bulan Terakhir
            $anchorDate = Carbon::createFromDate($selectedTahun, $selectedBulan, 1);
            $startDate = $anchorDate->copy()->subMonths(5)->startOfMonth()->format('Y-m-d');
            $endDate = $anchorDate->copy()->endOfMonth()->format('Y-m-d');

            $bkmGrouped = KasBkm::whereBetween('tanggal', [$startDate, $endDate])
                ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(debet) as total_debet, SUM(kredit) as total_kredit")
                ->groupBy('ym')
                ->get()
                ->keyBy('ym');

            $raabGrouped = KasRaab::whereBetween('tanggal', [$startDate, $endDate])
                ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(debet) as total_debet, SUM(kredit) as total_kredit")
                ->groupBy('ym')
                ->get()
                ->keyBy('ym');

            for ($i = 5; $i >= 0; $i--) {
                $date = $anchorDate->copy()->subMonths($i);
                $ym   = $date->format('Y-m');
                $m    = $date->month;
                $y    = $date->year;
                $chartLabels[] = $namaBulan[$m - 1] . ' ' . $y;

                $dBkm  = (float) ($bkmGrouped[$ym]->total_debet ?? 0);
                $kBkm  = (float) ($bkmGrouped[$ym]->total_kredit ?? 0);
                $dRaab = (float) ($raabGrouped[$ym]->total_debet ?? 0);
                $kRaab = (float) ($raabGrouped[$ym]->total_kredit ?? 0);

                $chartMasuk[]  = $dBkm + $dRaab;
                $chartKeluar[] = $kBkm + $kRaab;

                $totalDebetBkm  += $dBkm;
                $totalKreditBkm += $kBkm;
                $totalDebetRaab  += $dRaab;
                $totalKreditRaab += $kRaab;
            }

            $periodeLabel = '6 Bulan Terakhir';
        }

        $saldoBkm  = $totalDebetBkm - $totalKreditBkm;
        $saldoRaab = $totalDebetRaab - $totalKreditRaab;

        $totalMasuk  = $totalDebetBkm + $totalDebetRaab;
        $totalKeluar = $totalKreditBkm + $totalKreditRaab;
        $saldoAkhir  = $totalMasuk - $totalKeluar;

        // Get saldo terkini (real-time) dari transaksi terakhir
        $saldoTerkiniBkm = KasBkm::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo_kas') ?? 0;
        
        $saldoTerkiniRaab = KasRaab::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;
        
        $saldoTerkiniTotal = $saldoTerkiniBkm + $saldoTerkiniRaab;

        $rerataMasuk  = count($chartMasuk) > 0 ? array_sum($chartMasuk) / count($chartMasuk) : 0;
        $rerataKeluar = count($chartKeluar) > 0 ? array_sum($chartKeluar) / count($chartKeluar) : 0;

        // 5 entri Kas BKM terbaru
        $terbaru = KasBkm::orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalDebetBkm', 'totalKreditBkm', 'saldoBkm',
            'totalDebetRaab', 'totalKreditRaab', 'saldoRaab',
            'totalMasuk', 'totalKeluar', 'saldoAkhir',
            'saldoTerkiniBkm', 'saldoTerkiniRaab', 'saldoTerkiniTotal',
            'chartLabels', 'chartMasuk', 'chartKeluar',
            'rerataMasuk', 'rerataKeluar',
            'terbaru', 'filterMode', 'selectedBulan', 'selectedTahun', 'periodeLabel',
            'availableYears'
        ));
    }
}
