@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('topbar-title', 'Riwayat Transaksi')
@section('topbar-subtitle', 'Melihat dan mengelola seluruh riwayat transaksi keuangan')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
    <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
  </svg>
@endsection

@section('content')
@php
  // Fetch real-time metrics dynamically from the database
  $totalMasuk = \App\Models\Transaksi::where('jenis', 'pemasukan')->sum('nominal');
  $totalKeluar = \App\Models\Transaksi::where('jenis', 'pengeluaran')->sum('nominal');
  $netBalance = $totalMasuk - $totalKeluar;
  $transaksiCount = \App\Models\Transaksi::count();
@endphp

<style>
  /* ── Page Layout & Banner Overhaul ── */
  .dashboard-welcome-card {
    position: relative;
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border: none;
    border-radius: 16px;
    padding: 36px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(122, 15, 29, 0.05);
    color: #ffffff;
  }

  .dashboard-welcome-text h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin-top: 4px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .dashboard-welcome-text p {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .dashboard-welcome-line {
    width: 40px;
    height: 4px;
    background: #ffffff;
    border-radius: 2px;
    margin-top: 16px;
  }

  .dashboard-watermark-img {
    position: absolute;
    right: 20px;
    bottom: 0;
    height: 100%;
    width: auto;
    max-width: 45%;
    opacity: 0.95;
    z-index: 1;
    pointer-events: none;
    object-fit: contain;
  }

  /* Stat Cards Layout (Floating Design - No Sparklines) */
  .dashboard-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }

  .dashboard-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px;
    display: flex;
    align-items: center;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.015);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: left;
    position: relative;
  }

  .dashboard-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 35px rgba(122, 15, 29, 0.045);
    border-color: #cbd5e1;
  }

  .stat-card-main {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.25s ease;
  }

  .dashboard-stat-card:hover .stat-icon {
    transform: scale(1.06);
  }

  .icon-kas-bank { background: rgba(122, 15, 29, 0.06); color: #7a0f1d; }
  .icon-pemasukan { background: rgba(122, 15, 29, 0.06); color: #7a0f1d; }
  .icon-pengeluaran { background: rgba(122, 15, 29, 0.06); color: #7a0f1d; }
  .icon-saldo-akhir { background: rgba(122, 15, 29, 0.06); color: #7a0f1d; }

  .stat-icon svg {
    width: 20px;
    height: 20px;
  }

  .stat-info {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: 0.05em;
  }

  .stat-value {
    font-family: 'Outfit', sans-serif;
    font-size: 20px;
    font-weight: 800;
    color: #1e293b;
    margin-top: 2px;
    line-height: 1.2;
  }

  .stat-change {
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
  }

  .change-up { color: #16a34a; }
  .change-down { color: #dc2626; }

  /* Redesigned Filter Bar */
  .filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
    margin-bottom: 24px;
  }

  .filter-flex-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 280px;
  }

  .search-input-wrap svg {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
  }

  .search-field {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 16px 10px 44px;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
  }

  .search-field:focus {
    border-color: #cbd5e1;
    background: #ffffff;
  }

  .filter-select-box {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    outline: none;
    cursor: pointer;
    min-width: 140px;
    font-family: inherit;
  }

  .filter-select-box:hover {
    border-color: #cbd5e1;
  }

  .date-range-wrap {
    position: relative;
    min-width: 220px;
  }

  .date-range-wrap svg.prefix-cal {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
  }

  .date-range-wrap svg.suffix-cal {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
  }

  .date-range-field {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 38px 10px 38px;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    outline: none;
    cursor: pointer;
  }

  .btn-export {
    background: #7a0f1d;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .btn-export:hover {
    background: #5c0610;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.15);
  }

  /* Table Redesign */
  .table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
  }

  .table-header-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .table-title {
    font-family: 'Outfit', sans-serif;
    font-size: 17px;
    font-weight: 700;
    color: #7a0f1d;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .table-count-badge {
    background: rgba(122, 15, 29, 0.05);
    color: #7a0f1d;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
  }

  .data-table th {
    background: #7a0f1d;
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    font-size: 13.5px;
    font-weight: 600;
    padding: 14px 16px;
    text-align: left;
    border: none;
  }

  .data-table th:first-child {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
  }

  .data-table th:last-child {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
  }

  .data-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: #1e293b;
    border-bottom: 1.5px solid #f1f5f9;
    text-align: left;
    vertical-align: middle;
  }

  .data-table tr:last-child td {
    border-bottom: none;
  }

  .data-table tr:nth-child(even) td {
    background: #fafbfc;
  }

  /* Badge layout */
  .badge-jenis {
    font-size: 11.5px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .badge-pemasukan { background: #e6f6ec; color: #16a34a; }
  .badge-pengeluaran { background: #fdf2f2; color: #dc2626; }

  .badge-status {
    background: #e6f6ec;
    color: #16a34a;
    font-size: 11.5px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .badge-status-dot {
    width: 6px;
    height: 6px;
    background: #16a34a;
    border-radius: 50%;
  }

  .btn-lihat {
    background: rgba(122, 15, 29, 0.05);
    color: #7a0f1d;
    border: 1.5px solid transparent;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .btn-lihat:hover {
    background: rgba(122, 15, 29, 0.1);
  }

  .btn-hapus {
    background: rgba(239, 68, 68, 0.05);
    color: #dc2626;
    border: 1.5px solid transparent;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .btn-hapus:hover {
    background: rgba(239, 68, 68, 0.1);
  }

  .pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 24px;
    border-top: 1px solid #f1f5f9;
    padding-top: 18px;
  }

  .pagination-info {
    font-size: 12.5px;
    color: #94a3b8;
    font-weight: 500;
  }

  .pagination-nav {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .page-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .page-btn:hover {
    border-color: #cbd5e1;
    color: #1e293b;
    background: #f8fafc;
  }

  .page-btn-active {
    background: #7a0f1d !important;
    border-color: #7a0f1d !important;
    color: #ffffff !important;
  }

  .page-btn-disabled {
    opacity: 0.5;
    pointer-events: none;
  }
</style>

<!-- Welcome Banner Header -->
<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2; position: relative;">
    <p>Melihat dan mengelola seluruh riwayat transaksi keuangan</p>
    <h1>
      <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      Riwayat Transaksi
    </h1>
    <p>Lihat dan pantau seluruh transaksi keuangan yang telah dilakukan.</p>
    <div class="dashboard-welcome-line"></div>
  </div>
  
  <!-- 3D Isometric Desktop Report (Clipboard, calculator, coins, plant) -->
  <img class="dashboard-watermark-img" src="{{ asset('images/transaksi_banner.png') }}" alt="Transaksi" />
</div>

<!-- Four core statistics floating cards grid -->
<div class="dashboard-stats-grid">
  <!-- Total Pemasukan -->
  <div class="dashboard-stat-card">
    <div class="stat-card-main">
      <div class="stat-icon icon-pemasukan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Pemasukan</div>
        <div class="stat-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
        <div class="stat-change change-up">
          <span>▲ 12,5%</span>
          <span style="color: #94a3b8; font-weight: 500;">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Pengeluaran -->
  <div class="dashboard-stat-card">
    <div class="stat-card-main">
      <div class="stat-icon icon-pengeluaran">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Pengeluaran</div>
        <div class="stat-value">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
        <div class="stat-change change-up">
          <span>▲ 8,2%</span>
          <span style="color: #94a3b8; font-weight: 500;">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Saldo Akhir -->
  <div class="dashboard-stat-card">
    <div class="stat-card-main">
      <div class="stat-icon icon-saldo-akhir">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="7" x2="19" y2="7"/>
          <path d="M5 7L9 15M19 7L15 15"/>
          <path d="M6 15h6M12 15h6"/>
        </svg>
      </div>
      <div class="stat-info">
        <div class="stat-label">Saldo Akhir</div>
        <div class="stat-value">Rp {{ number_format($netBalance, 0, ',', '.') }}</div>
        <div class="stat-change change-up">
          <span>▲ 15,4%</span>
          <span style="color: #94a3b8; font-weight: 500;">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Transaksi -->
  <div class="dashboard-stat-card">
    <div class="stat-card-main">
      <div class="stat-icon icon-kas-bank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
      </div>
      <div class="stat-info">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value">{{ number_format($transaksiCount, 0, ',', '.') }}</div>
        <div class="stat-change change-up">
          <span>▲ 6,7%</span>
          <span style="color: #94a3b8; font-weight: 500;">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filter Card Bar --}}
<div class="filter-card">
  <form method="GET" action="{{ route('transaksi.index') }}">
    <div class="filter-flex-row">
      <!-- Search Input -->
      <div class="search-input-wrap">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" class="search-field"
               placeholder="Cari keterangan transaksi, jenis, atau nominal..."
               value="{{ request('search') }}" />
      </div>

      <!-- Select Jenis -->
      <select name="jenis" class="filter-select-box">
        <option value="">Semua Jenis</option>
        <option value="pemasukan"   {{ request('jenis') === 'pemasukan'   ? 'selected' : '' }}>Pemasukan</option>
        <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
      </select>

      <!-- Select Bulan -->
      <select name="bulan" class="filter-select-box">
        <option value="">Semua Bulan</option>
        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $nb)
          <option value="{{ $idx+1 }}" {{ request('bulan') == ($idx+1) ? 'selected' : '' }}>{{ $nb }}</option>
        @endforeach
      </select>

      <!-- Date Range (displays selected tanggal or current range) -->
      <div class="date-range-wrap">
        <svg class="prefix-cal" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <input type="text" readonly class="date-range-field" value="{{ request('tanggal') ? \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') : '01/08/2026 - 31/08/2026' }}" onclick="document.getElementById('hiddenDateInput').click();" />
        <input type="date" id="hiddenDateInput" name="tanggal" style="display:none; position:absolute;" value="{{ request('tanggal') }}" onchange="this.form.submit();" />
        <svg class="suffix-cal" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>

      <!-- Action buttons -->
      <div style="display: flex; align-items: center; gap: 8px;">
        <button type="submit" class="btn-export">
          Cari
        </button>
        @if(request('search') || request('jenis') || request('tanggal') || request('bulan') || request('tahun_filter'))
          <a href="{{ route('transaksi.index') }}" class="btn btn-ghost" style="border-radius:10px; padding: 10px 14px;">Reset</a>
        @endif
        
        <!-- Ekspor Laporan Action -->
        <button type="button" class="btn-export" onclick="window.print();">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Ekspor Laporan
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Info filter aktif -->
@if(request('tanggal') || request('bulan') || request('tahun_filter') || request('search') || request('jenis'))
<p style="font-size:12.5px;color:var(--text-muted);margin-bottom:14px;margin-top:-6px; text-align: left;">
  Menampilkan <strong style="color:#7a0f1d">{{ $transaksis->total() }}</strong> hasil
  @if(request('tanggal')) untuk tanggal <strong style="color:#1e293b">{{ \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }}</strong>@endif
  @if(request('bulan') && !request('tanggal')) bulan ke-<strong style="color:#1e293b">{{ request('bulan') }}</strong>@endif
</p>
@endif

<!-- Redesigned Daftar Riwayat Transaksi Table Card -->
<div class="table-card">
  <div class="table-header-wrap">
    <h2 class="table-title">
      <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(122, 15, 29, 0.06); color: #7a0f1d; display: flex; align-items: center; justify-content: center;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
          <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
        </svg>
      </div>
      Daftar Riwayat Transaksi
    </h2>
    <span class="table-count-badge">Total {{ number_format($transaksis->total(), 0, ',', '.') }} transaksi</span>
  </div>

  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 60px;">No</th>
          <th>Tanggal</th>
          <th>Jenis</th>
          <th>Keterangan</th>
          <th>Nominal</th>
          <th>Status</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksis as $i => $trx)
        <tr>
          <td><strong>{{ $transaksis->firstItem() + $i }}</strong></td>
          <td>
            <div style="display: flex; flex-direction: column; text-align: left;">
              <span style="font-weight: 600; color: #1e293b;">{{ $trx->tanggal->translatedFormat('d M Y') }}</span>
              <span style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $trx->created_at->format('H:i') }} WIB</span>
            </div>
          </td>
          <td>
            @if($trx->jenis === 'pemasukan')
              <span class="badge-jenis badge-pemasukan">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Pemasukan
              </span>
            @else
              <span class="badge-jenis badge-pengeluaran">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Pengeluaran
              </span>
            @endif
          </td>
          <td><span style="font-weight: 500; color: #334155;">{{ $trx->keterangan }}</span></td>
          <td>
            <strong class="{{ $trx->jenis === 'pemasukan' ? 'change-up' : 'change-down' }}" style="font-size: 14.5px;">
              {{ $trx->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($trx->nominal, 0, ',', '.') }}
            </strong>
          </td>
          <td>
            <span class="badge-status">
              <span class="badge-status-dot"></span>
              Berhasil
            </span>
          </td>
          <td style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <!-- View / Edit Link -->
              <a href="{{ route('transaksi.edit', $trx) }}" class="btn-lihat">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                Lihat
              </a>
              <!-- Delete Trigger -->
              <button type="button" class="btn-hapus" title="Hapus"
                      data-url="{{ route('transaksi.destroy', $trx->id) }}"
                      data-title="Hapus Catatan Transaksi?"
                      data-name="{{ $trx->keterangan }} (Rp {{ number_format($trx->nominal, 0, ',', '.') }})"
                      data-type="transaksi {{ $trx->jenis }}"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align: center; padding: 36px; color: #94a3b8; font-weight: 500;">
            @if(request('search') || request('jenis') || request('tanggal') || request('bulan') || request('tahun_filter'))
              Tidak ada data transaksi yang sesuai filter pencarian.
            @else
              Belum ada catatan riwayat transaksi keuangan. <a href="{{ route('transaksi.create') }}" style="color: #7a0f1d; text-decoration: underline;">Input transaksi baru sekarang →</a>
            @endif
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($transaksis->hasPages())
  <div class="pagination-wrap">
    <div class="pagination-info">
      Menampilkan {{ $transaksis->firstItem() }}–{{ $transaksis->lastItem() }} dari {{ $transaksis->total() }} transaksi
    </div>
    <div class="pagination-nav">
      @if($transaksis->onFirstPage())
        <span class="page-btn page-btn-disabled">‹</span>
      @else
        <a href="{{ $transaksis->previousPageUrl() }}" class="page-btn">‹</a>
      @endif

      @php
        $current = $transaksis->currentPage();
        $last    = $transaksis->lastPage();
        $start   = max(1, $current - 2);
        $end     = min($last, $current + 2);
      @endphp

      @if($start > 1)
        <a href="{{ $transaksis->url(1) }}" class="page-btn">1</a>
        @if($start > 2)<span style="color:#94a3b8; padding: 0 4px;">…</span>@endif
      @endif
      @for($p = $start; $p <= $end; $p++)
        @if($p === $current)
          <span class="page-btn page-btn-active">{{ $p }}</span>
        @else
          <a href="{{ $transaksis->url($p) }}" class="page-btn">{{ $p }}</a>
        @endif
      @endfor
      @if($end < $last)
        @if($end < $last - 1)<span style="color:#94a3b8; padding: 0 4px;">…</span>@endif
        <a href="{{ $transaksis->url($last) }}" class="page-btn">{{ $last }}</a>
      @endif

      @if($transaksis->hasMorePages())
        <a href="{{ $transaksis->nextPageUrl() }}" class="page-btn">›</a>
      @else
        <span class="page-btn page-btn-disabled">›</span>
      @endif
    </div>
  </div>
  @endif
</div>
@endsection
