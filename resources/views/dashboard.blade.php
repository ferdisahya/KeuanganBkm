@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard')
@section('topbar-subtitle', 'Ringkasan data keuangan dan metrik utama PT BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
  </svg>
@endsection

@section('content')
<style>
  /* ── Dashboard Page Overhaul Styling ── */
  .dashboard-welcome-card {
    position: relative;
    background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%);
    border: 1px solid rgba(223, 187, 109, 0.3);
    border-radius: 16px;
    padding: 32px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(122, 15, 29, 0.08);
    color: #ffffff;
  }

  .dashboard-welcome-text h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 30px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin-top: 4px;
    margin-bottom: 6px;
  }

  .dashboard-welcome-text p {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .dashboard-welcome-line {
    width: 40px;
    height: 4px;
    background: #dfbb6d;
    border-radius: 2px;
    margin-top: 14px;
  }

  .dashboard-watermark-svg {
    position: absolute;
    right: 20px;
    bottom: 10px;
    height: 100%;
    width: auto;
    max-width: 50%;
    opacity: 0.85;
    pointer-events: none;
    z-index: 1;
  }

  /* Executive Summary Card Grid (Matching Laporan Tahunan) */
  .summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 24px;
  }

  .summary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.015);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
  }

  .summary-card-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  }

  .icon-green { background: #e6f6ec; color: #16a34a; }
  .icon-red { background: #fdf2f2; color: #dc2626; }
  .icon-maroon { background: #fdf2f2; color: #7a0f1d; }

  .summary-card-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.5px;
  }

  .summary-card-value {
    font-size: 24px;
    font-weight: 800;
    font-family: 'Outfit', sans-serif;
    margin-top: 4px;
    line-height: 1.2;
  }

  .summary-card-subtext {
    font-size: 11.5px;
    color: #94a3b8;
    margin-top: 10px;
    font-weight: 500;
  }

  /* Breakdown Cards Grid (BKM vs RAAB) */
  .breakdown-card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
  }

  .breakdown-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.015);
  }

  .breakdown-card-title {
    font-family: 'Outfit', sans-serif;
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 16px 0;
  }

  .breakdown-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
  }

  .breakdown-row:last-child {
    border-bottom: none;
    font-weight: 800;
    padding-top: 14px;
  }

  .breakdown-label {
    font-size: 13px;
    color: #64748b;
  }

  .breakdown-value {
    font-size: 14.5px;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
  }

  /* Split Content Grid (Chart + Side Panel) */
  .dashboard-content-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 20px;
    align-items: start;
  }

  .dashboard-chart-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.005);
  }

  .chart-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .chart-card-title {
    font-family: 'Outfit', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
  }

  .chart-select-dropdown {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12.5px;
    color: #1e293b;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    font-family: inherit;
  }

  .chart-select-dropdown:hover {
    border-color: #cbd5e1;
  }

  .chart-canvas-container {
    height: 250px;
    position: relative;
    margin-bottom: 20px;
  }

  /* Average stats below chart */
  .chart-averages-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    border-top: 1px solid #f1f5f9;
    padding-top: 20px;
  }

  .average-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid rgba(0, 0, 0, 0.01);
  }

  .avg-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .avg-pemasukan { background: #7a0f1d; color: #ffffff; }
  .avg-pengeluaran { background: #dfbb6d; color: #ffffff; }

  .avg-info {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .avg-label {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 600;
  }

  .avg-value {
    font-family: 'Outfit', sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-top: 2px;
  }

  /* Recent Transactions Widget */
  .recent-transactions-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.005);
  }

  .recent-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .btn-view-all {
    font-size: 11.5px;
    color: #64748b;
    font-weight: 600;
    text-decoration: none;
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    padding: 5px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  .btn-view-all:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
  }

  .recent-trx-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .recent-trx-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1.5px solid #f8f9fa;
  }

  .recent-trx-item:last-child {
    border-bottom: none;
  }

  .recent-trx-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .recent-trx-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .recent-in { background: #e6f6ec; color: #16a34a; }
  .recent-out { background: #fdf2f2; color: #7a0f1d; }

  .recent-trx-details {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .recent-trx-desc {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .recent-trx-date {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 1px;
  }

  .recent-trx-amount {
    font-size: 13px;
    font-weight: 700;
  }

  /* Responsive grid modifications */
  @media (max-width: 1100px) {
    .summary-cards-grid {
      grid-template-columns: 1fr;
    }
    .breakdown-card-grid {
      grid-template-columns: 1fr;
    }
  }
  @media (max-width: 991px) {
    .dashboard-content-grid {
      grid-template-columns: 1fr;
    }
  }
  @media (max-width: 576px) {
    .dashboard-welcome-card {
      padding: 24px 20px;
    }
    .dashboard-welcome-text h1 {
      font-size: 22px;
    }
    .dashboard-watermark-svg {
      display: none;
    }
  }
</style>

<!-- Welcome Banner Header -->
<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2; position: relative;">
    <p>Selamat datang,</p>
    <h1>{{ auth()->user()->name }}</h1>
    <p>Berikut ringkasan eksekutif keuangan PT BKM ({{ $periodeLabel }}).</p>
    <div class="dashboard-welcome-line"></div>
  </div>
  
  <!-- Abstract 3D Isometric Chart Illustration -->
  <svg class="dashboard-watermark-svg" viewBox="0 0 320 120" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="210" cy="50" r="40" fill="#ffffff" fill-opacity="0.12" filter="blur(10px)" />
    <path d="M 120,70 L 128,66 L 136,70 L 136,80 L 128,84 L 120,80 Z" fill="#7a0f1d"/>
    <path d="M 120,70 L 128,66 L 136,70 L 128,74 Z" fill="#b82537"/>
    <path d="M 120,70 L 128,74 L 128,84 L 120,80 Z" fill="#991524"/>
    <path d="M 128,74 L 136,70 L 136,80 L 128,84 Z" fill="#5c0610"/>

    <path d="M 135,60 L 143,56 L 151,60 L 151,75 L 143,79 L 135,75 Z" fill="#7a0f1d"/>
    <path d="M 135,60 L 143,56 L 151,60 L 143,64 Z" fill="#b82537"/>
    <path d="M 135,60 L 143,64 L 143,79 L 135,75 Z" fill="#991524"/>
    <path d="M 143,64 L 151,60 L 151,75 L 143,79 Z" fill="#5c0610"/>

    <path d="M 150,45 L 158,41 L 166,45 L 166,70 L 158,74 L 150,70 Z" fill="#7a0f1d"/>
    <path d="M 150,45 L 158,41 L 166,45 L 158,49 Z" fill="#b82537"/>
    <path d="M 150,45 L 158,49 L 158,74 L 150,70 Z" fill="#991524"/>
    <path d="M 158,49 L 166,45 L 166,70 L 158,74 Z" fill="#5c0610"/>

    <path d="M 170,85 L 230,55 L 290,85 L 230,115 Z" fill="#e2e8f0"/>
    <path d="M 170,85 L 230,115 L 230,119 L 170,89 Z" fill="#cbd5e1"/>
    <path d="M 230,115 L 290,85 L 290,89 L 230,119 Z" fill="#94a3b8"/>
    <path d="M 220,103 L 240,93 L 250,98 L 230,108 Z" fill="#cbd5e1"/>

    <path d="M 230,55 L 290,85 L 290,35 L 230,5 Z" fill="#334155"/>
    <path d="M 234,53 L 286,79 L 286,39 L 234,13 Z" fill="#1e3a8a"/>
    <path d="M 234,53 L 286,79 L 286,39 L 234,13 Z" fill="#ffffff" fill-opacity="0.1"/>
    
    <path d="M 240,42 L 252,36 L 264,48 L 276,28 L 282,32" fill="none" stroke="#60a5fa" stroke-width="1.8" stroke-linecap="round"/>
    <text x="245" y="27" fill="#ffffff" font-family="'Outfit', sans-serif" font-weight="700" font-size="8" transform="skewY(26)">Rp</text>
  </svg>
</div>

<!-- Executive Summary 3-Card Grid (Exact Laporan Tahunan Layout) -->
<div class="summary-cards-grid">
  <!-- Total Pemasukan -->
  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-green">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
      </div>
      <div class="summary-card-label">Total Pemasukan Keseluruhan</div>
      <div class="summary-card-value count-up" data-value="{{ $totalMasuk }}" style="color: #16a34a;">
        {{ $totalMasuk < 0 ? '-Rp ' . number_format(abs($totalMasuk), 0, ',', '.') : 'Rp ' . number_format($totalMasuk, 0, ',', '.') }}
      </div>
    </div>
    <div class="summary-card-subtext">Akumulasi debet (BKM + RAAB) {{ strtolower($periodeLabel) }}</div>
  </div>

  <!-- Total Pengeluaran -->
  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-red">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
      </div>
      <div class="summary-card-label">Total Pengeluaran Keseluruhan</div>
      <div class="summary-card-value count-up" data-value="{{ $totalKeluar }}" style="color: #dc2626;">
        {{ $totalKeluar < 0 ? '-Rp ' . number_format(abs($totalKeluar), 0, ',', '.') : 'Rp ' . number_format($totalKeluar, 0, ',', '.') }}
      </div>
    </div>
    <div class="summary-card-subtext">Akumulasi kredit (BKM + RAAB) {{ strtolower($periodeLabel) }}</div>
  </div>

  <!-- Saldo Akhir -->
  <div class="summary-card">
    <div>
      <div class="summary-card-icon {{ $saldoAkhir < 0 ? 'icon-red' : 'icon-maroon' }}">
        @if($saldoAkhir < 0)
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
        @else
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        @endif
      </div>
      <div class="summary-card-label">Saldo Akhir</div>
      <div class="summary-card-value count-up" data-value="{{ $saldoAkhir }}" style="color: {{ $saldoAkhir < 0 ? '#dc2626' : '#7a0f1d' }};">
        {{ $saldoAkhir < 0 ? '-Rp ' . number_format(abs($saldoAkhir), 0, ',', '.') : 'Rp ' . number_format($saldoAkhir, 0, ',', '.') }}
      </div>
    </div>
    <div class="summary-card-subtext">Net kas (Pemasukan - Pengeluaran) {{ strtolower($periodeLabel) }}</div>
  </div>
</div>

<!-- Breakdown BKM & RAAB 2-Card Grid (Exact Laporan Tahunan Layout) -->
<div class="breakdown-card-grid">
  <!-- Breakdown Kas BKM -->
  <div class="breakdown-card">
    <h3 class="breakdown-card-title" style="color: #7a0f1d;">Breakdown Kas BKM</h3>
    <div class="breakdown-row">
      <span class="breakdown-label">Debet Kas BKM:</span>
      <span class="breakdown-value count-up" data-value="{{ $totalDebetBkm }}" style="color: #16a34a;">
        Rp {{ number_format($totalDebetBkm, 0, ',', '.') }}
      </span>
    </div>
    <div class="breakdown-row">
      <span class="breakdown-label">Kredit Kas BKM:</span>
      <span class="breakdown-value count-up" data-value="{{ $totalKreditBkm }}" style="color: #dc2626;">
        Rp {{ number_format($totalKreditBkm, 0, ',', '.') }}
      </span>
    </div>
    <div class="breakdown-row">
      <span class="breakdown-label" style="color: #1e293b; font-weight: 700;">Saldo BKM:</span>
      <span class="breakdown-value count-up" data-value="{{ $saldoBkm }}" style="color: {{ $saldoBkm < 0 ? '#dc2626' : '#7a0f1d' }}; font-size: 15.5px;">
        {{ $saldoBkm < 0 ? '-Rp ' . number_format(abs($saldoBkm), 0, ',', '.') : 'Rp ' . number_format($saldoBkm, 0, ',', '.') }}
      </span>
    </div>
  </div>

  <!-- Breakdown Kas RAAB -->
  <div class="breakdown-card">
    <h3 class="breakdown-card-title" style="color: #2563eb;">Breakdown Kas RAAB</h3>
    <div class="breakdown-row">
      <span class="breakdown-label">Debet Kas RAAB:</span>
      <span class="breakdown-value count-up" data-value="{{ $totalDebetRaab }}" style="color: #2563eb;">
        Rp {{ number_format($totalDebetRaab, 0, ',', '.') }}
      </span>
    </div>
    <div class="breakdown-row">
      <span class="breakdown-label">Kredit Kas RAAB:</span>
      <span class="breakdown-value count-up" data-value="{{ $totalKreditRaab }}" style="color: #9333ea;">
        Rp {{ number_format($totalKreditRaab, 0, ',', '.') }}
      </span>
    </div>
    <div class="breakdown-row">
      <span class="breakdown-label" style="color: #1e293b; font-weight: 700;">Saldo RAAB:</span>
      <span class="breakdown-value count-up" data-value="{{ $saldoRaab }}" style="color: {{ $saldoRaab < 0 ? '#dc2626' : '#3730a3' }}; font-size: 15.5px;">
        {{ $saldoRaab < 0 ? '-Rp ' . number_format(abs($saldoRaab), 0, ',', '.') : 'Rp ' . number_format($saldoRaab, 0, ',', '.') }}
      </span>
    </div>
  </div>
</div>

<!-- Main Split Grid (Chart Performance & Recent Transactions) -->
<div class="dashboard-content-grid">
  <!-- Left Side: Performance Chart & Averages -->
  <div class="dashboard-chart-card">
    <div class="chart-card-header" style="flex-wrap: wrap; gap: 12px;">
      <h3 class="chart-card-title" style="margin: 0;">Grafik Arus Keuangan (BKM + RAAB)</h3>
      
      <!-- Interactive Dynamic Filter Form -->
      <form method="GET" action="{{ route('dashboard') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <select name="filter_mode" id="filter_mode_select" onchange="handleFilterModeChange(this)" class="chart-select-dropdown" style="padding: 6px 12px; font-weight: 600; font-size: 12.5px;">
          <option value="semua" {{ $filterMode === 'semua' ? 'selected' : '' }}>Semua Waktu</option>
          <option value="tahun" {{ $filterMode === 'tahun' ? 'selected' : '' }}>Filter Tahun</option>
          <option value="bulan" {{ $filterMode === 'bulan' ? 'selected' : '' }}>Filter Bulan</option>
          <option value="6_bulan" {{ $filterMode === '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
        </select>

        <select name="bulan" id="select_bulan_input" class="chart-select-dropdown" style="padding: 6px 10px; font-size: 12.5px; {{ $filterMode !== 'bulan' ? 'display:none;' : '' }}">
          @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $mNum => $mName)
            <option value="{{ $mNum }}" {{ $selectedBulan == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
          @endforeach
        </select>

        <select name="tahun" id="select_tahun_input" class="chart-select-dropdown" style="padding: 6px 10px; font-size: 12.5px; {{ in_array($filterMode, ['semua', '6_bulan']) ? 'display:none;' : '' }}">
          @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endforeach
        </select>

        <button type="submit" class="btn-primary-maroon" style="padding: 6px 14px; font-size: 12px; border-radius: 8px; box-shadow: none;">Terapkan</button>
      </form>
    </div>
    
    <div class="chart-canvas-container">
      <canvas id="monthlyChart"></canvas>
    </div>

    <!-- Period averages block -->
    <div class="chart-averages-row">
      <!-- Rerata Pemasukan -->
      <div class="average-card">
        <div class="avg-icon avg-pemasukan">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            <polyline points="17 6 23 6 23 12"/>
          </svg>
        </div>
        <div class="avg-info">
          <span class="avg-label">Rata-rata Pemasukan</span>
          <span class="avg-value count-up" data-value="{{ $rerataMasuk }}">
            {{ $rerataMasuk < 0 ? '-Rp ' . number_format(abs($rerataMasuk), 0, ',', '.') : 'Rp ' . number_format($rerataMasuk, 0, ',', '.') }}
          </span>
        </div>
      </div>

      <!-- Rerata Pengeluaran -->
      <div class="average-card">
        <div class="avg-icon avg-pengeluaran">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
            <polyline points="17 18 23 18 23 12"/>
          </svg>
        </div>
        <div class="avg-info">
          <span class="avg-label">Rata-rata Pengeluaran</span>
          <span class="avg-value count-up" data-value="{{ $rerataKeluar }}">
            {{ $rerataKeluar < 0 ? '-Rp ' . number_format(abs($rerataKeluar), 0, ',', '.') : 'Rp ' . number_format($rerataKeluar, 0, ',', '.') }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Transaksi BKM Terbaru Widget -->
  <div>
    <div class="recent-transactions-card">
      <div class="recent-card-header">
        <h3 class="chart-card-title" style="margin-bottom: 0;">Transaksi BKM Terbaru</h3>
        <a href="{{ route('kas_bkm.index') }}" class="btn-view-all">Lihat Semua</a>
      </div>
      
      <div class="recent-trx-list">
        @forelse($terbaru as $trx)
          <div class="recent-trx-item">
            <div class="recent-trx-left">
              <div class="recent-trx-icon {{ (float)$trx->debet > 0 ? 'recent-in' : 'recent-out' }}">
                @if((float)$trx->debet > 0)
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                  </svg>
                @else
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                  </svg>
                @endif
              </div>
              <div class="recent-trx-details">
                <span class="recent-trx-desc" title="{{ $trx->uraian }}">{{ $trx->uraian }}</span>
                <span class="recent-trx-date">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}</span>
              </div>
            </div>
            
            <div class="recent-trx-amount {{ (float)$trx->debet > 0 ? 'val-green' : 'val-red' }}">
              {{ (float)$trx->debet > 0 ? '+' : '-' }}Rp {{ number_format((float)$trx->debet > 0 ? $trx->debet : $trx->kredit, 0, ',', '.') }}
            </div>
          </div>
        @empty
          <div class="empty-state-sm">Belum ada transaksi BKM terbaru</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Chart.js configuration without dots (pointRadius: 0)
  const ctx = document.getElementById('monthlyChart').getContext('2d');
  const labels  = @json($chartLabels);
  const masuk   = @json($chartMasuk);
  const keluar  = @json($chartKeluar);

  window.monthlyChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Pemasukan',
          data: masuk,
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22, 163, 74, 0.04)',
          borderWidth: 2.5,
          tension: 0.35,
          fill: true,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointHitRadius: 10,
        },
        {
          label: 'Pengeluaran',
          data: keluar,
          borderColor: '#dc2626',
          backgroundColor: 'rgba(220, 38, 38, 0.04)',
          borderWidth: 2.5,
          tension: 0.35,
          fill: true,
          pointRadius: 0,
          pointHoverRadius: 6,
          pointHitRadius: 10,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          align: 'center',
          labels: {
            color: '#64748b',
            font: { family: 'Inter', size: 12, weight: '500' },
            boxWidth: 8,
            boxHeight: 8,
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          backgroundColor: '#ffffff',
          titleColor: '#1e293b',
          titleFont: { family: 'Outfit', size: 12, weight: '700' },
          bodyColor: '#64748b',
          bodyFont: { family: 'Inter', size: 12 },
          borderColor: '#e2e8f0',
          borderWidth: 1,
          padding: 12,
          boxPadding: 6,
          usePointStyle: true,
          callbacks: {
            label: function(context) {
              return ' ' + context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          ticks: { color: '#94a3b8', font: { size: 11 } },
          grid: { display: false }
        },
        y: {
          ticks: {
            color: '#94a3b8',
            font: { size: 11 },
            callback: v => 'Rp ' + (Math.abs(v) >= 1000000000 ? (v/1000000000).toFixed(1)+'m' : (Math.abs(v) >= 1000000 ? (v/1000000).toFixed(0)+'jt' : v.toLocaleString('id-ID')))
          },
          grid: { color: '#f1f5f9' }
        }
      }
    }
  });

  // ── Count-up animation for numbers ──
  document.addEventListener('DOMContentLoaded', () => {
    const countUpElements = document.querySelectorAll('.count-up');
    
    countUpElements.forEach(el => {
      const target = parseFloat(el.getAttribute('data-value')) || 0;
      const isNegative = target < 0;
      const absoluteTarget = Math.abs(target);
      const duration = 800;
      let startTimestamp = null;
      
      const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        const current = easeProgress * absoluteTarget;
        
        const formatted = (isNegative ? '-Rp ' : 'Rp ') + Math.round(current).toLocaleString('id-ID');
        el.textContent = formatted;
        
        if (progress < 1) {
          window.requestAnimationFrame(step);
        } else {
          el.textContent = (isNegative ? '-Rp ' : 'Rp ') + Math.round(absoluteTarget).toLocaleString('id-ID');
        }
      };
      
      window.requestAnimationFrame(step);
    });
  });

  function toggleFilterInputs() {
    const selectEl = document.getElementById('filter_mode_select');
    const mode = selectEl ? selectEl.value : 'semua';
    const bulanInput = document.getElementById('select_bulan_input');
    const tahunInput = document.getElementById('select_tahun_input');

    if (mode === 'semua' || mode === '6_bulan') {
      if (bulanInput) bulanInput.style.display = 'none';
      if (tahunInput) tahunInput.style.display = 'none';
    } else if (mode === 'tahun') {
      if (bulanInput) bulanInput.style.display = 'none';
      if (tahunInput) tahunInput.style.display = 'inline-block';
    } else if (mode === 'bulan') {
      if (bulanInput) bulanInput.style.display = 'inline-block';
      if (tahunInput) tahunInput.style.display = 'inline-block';
    }
  }

  function handleFilterModeChange(selectEl) {
    toggleFilterInputs();
    if (selectEl.value === 'semua' || selectEl.value === '6_bulan') {
      selectEl.form.submit();
    }
  }
</script>
@endpush
