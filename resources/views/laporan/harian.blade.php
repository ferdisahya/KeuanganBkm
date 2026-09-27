@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('topbar-title', 'Laporan Harian')
@section('topbar-subtitle', 'Rekapitulasi transaksi keuangan harian PT BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; border: 1px solid rgba(223, 187, 109, 0.3); box-shadow: 0 8px 24px rgba(122, 15, 29, 0.12); position: relative; }
  .filter-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .form-control-custom { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #1e293b; outline: none; transition: border-color 0.2s; }
  .form-control-custom:focus { border-color: #dfbb6d; background: #ffffff; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(223, 187, 109, 0.4); cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2); }
  .btn-export-excel { background: #16a34a; color: #ffffff; font-weight: 600; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; border: none; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.2); }
  .btn-export-pdf { background: #dc2626; color: #ffffff; font-weight: 600; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; border: none; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2); cursor: pointer; }

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
    transition: all 0.2s ease;
  }
  .summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04); }
  .summary-card-icon {
    width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; flex-shrink: 0;
  }
  .icon-green { background: #e6f6ec; color: #16a34a; }
  .icon-red { background: #fdf2f2; color: #dc2626; }
  .icon-maroon { background: #fdf2f2; color: #7a0f1d; }

  .summary-card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; tracking: 0.5px; }
  .summary-card-value { font-size: 24px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-top: 4px; line-height: 1.2; }
  .summary-card-subtext { font-size: 11.5px; color: #94a3b8; margin-top: 8px; font-weight: 500; }

  .breakdown-card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  .breakdown-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
  }
</style>

@php
  $tglFormatted = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
@endphp

<div class="dashboard-welcome-card">
  <div>
    <span style="color:#dfbb6d; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">Modul Laporan</span>
    <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Laporan Harian ({{ $tglFormatted }})</h1>
    <p style="font-size:13.5px; opacity:0.85; font-weight:500;">Ringkasan eksekutif transaksi kas tanggal {{ $tglFormatted }}</p>
  </div>
</div>

<div class="filter-card">
  <form method="GET" action="{{ route('laporan.harian') }}" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
    <div>
      <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Pilih Tanggal</label>
      <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control-custom">
    </div>
    <div style="margin-top: auto;">
      <button type="submit" class="btn-primary-maroon">Terapkan Filter</button>
    </div>
    <div style="margin-top: auto; margin-left: auto; display: flex; gap: 10px;">
      <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn-export-excel">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Ekspor Excel
      </a>
      <button type="button" class="btn-export-pdf" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak Laporan
      </button>
    </div>
  </form>
</div>

<div class="summary-cards-grid">
  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-green">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
      </div>
      <div class="summary-card-label">Pemasukan Hari Ini</div>
      <div class="summary-card-value" style="color: #16a34a;">Rp {{ number_format($grandMasuk, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Debet kas masuk {{ $tglFormatted }}</div>
  </div>

  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-red">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
      </div>
      <div class="summary-card-label">Pengeluaran Hari Ini</div>
      <div class="summary-card-value" style="color: #dc2626;">Rp {{ number_format($grandKeluar, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Kredit kas keluar {{ $tglFormatted }}</div>
  </div>

  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-maroon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <div class="summary-card-label">Saldo Kas Neto</div>
      <div class="summary-card-value" style="color: #7a0f1d;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Selisih neto transaksi hari ini</div>
  </div>
</div>

<div class="breakdown-card-grid">
  <div class="breakdown-card">
    <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#7a0f1d; margin:0 0 16px 0;">Breakdown Kas BKM</h3>
    <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
      <span style="font-size:13px; color:#64748b;">Debet Kas BKM:</span>
      <span style="font-size:14px; font-weight:700; color:#16a34a;">Rp {{ number_format($totalDebetBkm ?? 0, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
      <span style="font-size:13px; color:#64748b;">Kredit Kas BKM:</span>
      <span style="font-size:14px; font-weight:700; color:#dc2626;">Rp {{ number_format($totalKreditBkm ?? 0, 0, ',', '.') }}</span>
    </div>
  </div>

  <div class="breakdown-card">
    <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#2563eb; margin:0 0 16px 0;">Breakdown Kas RAAB</h3>
    <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
      <span style="font-size:13px; color:#64748b;">Debet Kas RAAB:</span>
      <span style="font-size:14px; font-weight:700; color:#2563eb;">Rp {{ number_format($totalDebetRaab ?? 0, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
      <span style="font-size:13px; color:#64748b;">Kredit Kas RAAB:</span>
      <span style="font-size:14px; font-weight:700; color:#9333ea;">Rp {{ number_format($totalKreditRaab ?? 0, 0, ',', '.') }}</span>
    </div>
  </div>
</div>
@endsection
