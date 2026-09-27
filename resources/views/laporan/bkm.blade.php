@extends('layouts.app')
@section('title', 'Laporan Kas BKM')
@section('topbar-title', 'Laporan Kas BKM')
@section('topbar-subtitle', 'Rekapitulasi pencatatan kas BKM dengan filter fleksibel & ekspor')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
    <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; border: 1px solid rgba(223, 187, 109, 0.3); box-shadow: 0 8px 24px rgba(122, 15, 29, 0.12); position: relative; }
  .dashboard-welcome-card::after { content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 100%; background: radial-gradient(circle, rgba(223, 187, 109, 0.15) 0%, transparent 70%); pointer-events: none; }
  .filter-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .form-control-custom { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #1e293b; outline: none; transition: border-color 0.2s; }
  .form-control-custom:focus { border-color: #dfbb6d; background: #ffffff; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(223, 187, 109, 0.4); cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2); }
  .btn-export-excel { background: #16a34a; color: #ffffff; font-weight: 600; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; border: none; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.2); }
  .btn-export-pdf { background: #dc2626; color: #ffffff; font-weight: 600; padding: 10px 18px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; border: none; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2); }

  .summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
  .icon-blue { background: #eff6ff; color: #2563eb; }

  .summary-card-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; tracking: 0.5px; }
  .summary-card-value { font-size: 22px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-top: 4px; line-height: 1.2; }
  .summary-card-subtext { font-size: 11.5px; color: #94a3b8; margin-top: 8px; font-weight: 500; }
</style>

<div class="dashboard-welcome-card">
  <div>
    <span style="color:#dfbb6d; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">Modul Laporan</span>
    <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Laporan Kas BKM</h1>
    <p style="font-size:13.5px; opacity:0.85; font-weight:500;">Ringkasan eksekutif rekapitulasi debet, kredit, dan saldo BKM PT BKM</p>
  </div>
</div>

<div class="filter-card">
  <form method="GET" action="{{ route('laporan.bkm') }}" id="filterForm">
    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
      <div>
        <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Tipe Filter</label>
        <select name="filter_type" id="filterType" class="form-control-custom" onchange="toggleFilterFields()">
          <option value="bulan" {{ request('filter_type', 'bulan') === 'bulan' ? 'selected' : '' }}>Filter Per Bulan</option>
          <option value="tahun" {{ request('filter_type') === 'tahun' ? 'selected' : '' }}>Filter Per Tahun</option>
          <option value="range" {{ request('filter_type') === 'range' ? 'selected' : '' }}>Custom Tanggal (Start - End)</option>
        </select>
      </div>

      <div id="fieldBulan" style="display: {{ request('filter_type', 'bulan') === 'bulan' ? 'block' : 'none' }};">
        <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Bulan</label>
        <select name="bulan" class="form-control-custom">
          @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
            <option value="{{ $num }}" {{ request('bulan', now()->month) == $num ? 'selected' : '' }}>{{ $nama }}</option>
          @endforeach
        </select>
      </div>

      <div id="fieldTahun" style="display: {{ in_array(request('filter_type', 'bulan'), ['bulan', 'tahun']) ? 'block' : 'none' }};">
        <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Tahun</label>
        <select name="tahun" class="form-control-custom">
          @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ request('tahun', now()->year) == $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>

      <div id="fieldRangeStart" style="display: {{ request('filter_type') === 'range' ? 'block' : 'none' }};">
        <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Tanggal Mulai</label>
        <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" class="form-control-custom">
      </div>

      <div id="fieldRangeEnd" style="display: {{ request('filter_type') === 'range' ? 'block' : 'none' }};">
        <label style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 6px;">Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}" class="form-control-custom">
      </div>

      <div style="margin-top: auto;">
        <button type="submit" class="btn-primary-maroon">Terapkan Filter</button>
      </div>
    </div>
  </form>

  <div style="display: flex; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 16px; justify-content: flex-end;">
    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn-export-excel">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Ekspor Excel
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="btn-export-pdf">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Cetak PDF
    </a>
  </div>
</div>

{{-- Executive Summary Card Grid (No Data Table) --}}
<div class="summary-cards-grid">
  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-green">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
      </div>
      <div class="summary-card-label">Total Debet (Penerimaan)</div>
      <div class="summary-card-value" style="color: #16a34a;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Pemasukan kas masuk BKM terverifikasi</div>
  </div>

  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-red">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
      </div>
      <div class="summary-card-label">Total Kredit (Pengeluaran)</div>
      <div class="summary-card-value" style="color: #dc2626;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Pengeluaran operasional & kas keluar</div>
  </div>

  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-maroon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
      </div>
      <div class="summary-card-label">Saldo Kas Akhir</div>
      <div class="summary-card-value" style="color: #7a0f1d;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
    </div>
    <div class="summary-card-subtext">Selisih neto saldo kas BKM</div>
  </div>

  <div class="summary-card">
    <div>
      <div class="summary-card-icon icon-blue">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div class="summary-card-label">Total Transaksi Data</div>
      <div class="summary-card-value" style="color: #2563eb;">{{ number_format($transaksis->count(), 0, ',', '.') }} Data</div>
    </div>
    <div class="summary-card-subtext">Jumlah baris transaksi pada periode ini</div>
  </div>
</div>

<script>
  function toggleFilterFields() {
    const type = document.getElementById('filterType').value;
    document.getElementById('fieldBulan').style.display = (type === 'bulan') ? 'block' : 'none';
    document.getElementById('fieldTahun').style.display = (type === 'bulan' || type === 'tahun') ? 'block' : 'none';
    document.getElementById('fieldRangeStart').style.display = (type === 'range') ? 'block' : 'none';
    document.getElementById('fieldRangeEnd').style.display = (type === 'range') ? 'block' : 'none';
  }
</script>
@endsection
