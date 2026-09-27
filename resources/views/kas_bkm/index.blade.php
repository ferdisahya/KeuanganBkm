@extends('layouts.app')
@section('title', 'Kas BKM')
@section('topbar-title', 'Kas BKM')
@section('topbar-subtitle', 'Pencatatan Buku Kas Masuk & Kas Keluar BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
    <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
  </svg>
@endsection

@section('content')
<style>
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
    font-size: 30px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin-top: 4px;
    margin-bottom: 8px;
  }
  .dashboard-welcome-text p {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }
  .dashboard-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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
    gap: 16px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.015);
  }
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .icon-in { background: #e6f6ec; color: #16a34a; }
  .icon-out { background: #fdf2f2; color: #dc2626; }
  .icon-saldo { background: #fef3c7; color: #b45309; }
  .stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; tracking: 0.5px; }
  .stat-value { font-size: 20px; font-weight: 800; color: #1e293b; font-family: 'Outfit', sans-serif; margin-top: 2px; }
  
  .filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
  }
  .table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
  }
  .data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }
  .data-table th {
    background: #f8fafc;
    padding: 14px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
  }
  .data-table td {
    padding: 14px 18px;
    font-size: 13px;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
  }
  .data-table tr:hover td { background: #f8fafc; }
  .btn-primary-maroon {
    background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%);
    color: #ffffff;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2);
    border: none;
    cursor: pointer;
  }
  .btn-primary-maroon:hover { opacity: 0.95; color: #ffffff; }
  .form-control-custom {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
  }
  .form-control-custom:focus {
    border-color: #7a0f1d;
    background: #ffffff;
  }
  .icon-btn-edit {
    width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); color: #2563eb; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; text-decoration: none;
  }
  .icon-btn-edit:hover { background: #2563eb; color: #ffffff; }
  .icon-btn-delete {
    width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #dc2626; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; cursor: pointer;
  }
  .icon-btn-delete:hover { background: #dc2626; color: #ffffff; }
</style>

{{-- Header Banner --}}
<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2;">
    <p>Buku Kas Utama</p>
    <h1>Kas BKM PT BKM</h1>
    <p>Kelola pencatatan debet, kredit, dan saldo kas secara transparan</p>
  </div>
  <a href="{{ route('kas_bkm.create') }}" class="btn-primary-maroon" style="z-index: 2;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Transaksi BKM
  </a>
</div>

{{-- Summary Stats Grid --}}
<div class="dashboard-stats-grid">
  <div class="dashboard-stat-card">
    <div class="stat-icon icon-in">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Debet (Penerimaan)</div>
      <div class="stat-value" style="color:#16a34a;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card">
    <div class="stat-icon icon-out">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Kredit (Pengeluaran)</div>
      <div class="stat-value" style="color:#dc2626;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card">
    <div class="stat-icon icon-saldo" style="{{ $saldoAkhir < 0 ? 'background: #fdf2f2; color: #dc2626;' : '' }}">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><line x1="12" y1="4" x2="12" y2="20"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Saldo Kas BKM {{ request()->hasAny(['search', 'tanggal', 'bulan', 'tahun']) ? '(Terfilter)' : '' }}</div>
      <div class="stat-value" style="color: {{ $saldoAkhir < 0 ? '#dc2626' : '#7a0f1d' }};">
        {{ $saldoAkhir < 0 ? '-Rp ' . number_format(abs($saldoAkhir), 0, ',', '.') : 'Rp ' . number_format($saldoAkhir, 0, ',', '.') }}
      </div>
    </div>
  </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
  <form method="GET" action="{{ route('kas_bkm.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari uraian, rekening, item BKM..." class="form-control-custom" style="width: 100%;">
    </div>
    <div style="flex: 1; min-width: 140px;">
      <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control-custom" style="width: 100%;" title="Filter Tanggal">
    </div>
    <div style="width: 130px;">
      <select name="bulan" class="form-control-custom" style="width: 100%;">
        <option value="">-- Bulan --</option>
        @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $mNum => $mName)
          <option value="{{ $mNum }}" {{ request('bulan') == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
        @endforeach
      </select>
    </div>
    <div style="width: 110px;">
      <select name="tahun" class="form-control-custom" style="width: 100%;">
        <option value="">-- Tahun --</option>
        @for($y = date('Y'); $y >= 2020; $y--)
          <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    <button type="submit" class="btn-primary-maroon" style="padding: 10px 18px; font-size: 13px;">Filter</button>
    @if(request()->hasAny(['search', 'tanggal', 'bulan', 'tahun']))
      <a href="{{ route('kas_bkm.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; padding: 10px 14px;">Reset</a>
    @endif
  </form>
</div>

{{-- Data Table Card --}}
<div class="table-card">
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Tanggal</th>
          <th>Rekening BKM</th>
          <th>Subrekening BKM</th>
          <th>Item BKM</th>
          <th>Uraian</th>
          <th style="text-align: right;">Debet</th>
          <th style="text-align: right;">Kredit</th>
          <th style="text-align: right;">Saldo Kas</th>
          <th>Keterangan</th>
          <th style="text-align: center; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksis as $index => $row)
          <tr>
            <td style="font-weight: 600; color: #94a3b8;">{{ $transaksis->firstItem() + $index }}</td>
            <td style="font-weight: 600; white-space: nowrap;">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->rekeningBkm->nomer_rekening ?? '-' }}</div>
              <div style="font-size: 11px; color: #64748b;">{{ $row->rekeningBkm->nama_rekening ?? '-' }}</div>
            </td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->subrekeningBkm->nomer_subrekening ?? '-' }}</div>
              <div style="font-size: 11px; color: #64748b;">{{ $row->subrekeningBkm->nama_subrekening ?? '-' }}</div>
            </td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: #b45309;">{{ $row->itemBkm->nomer_item ?? '-' }}</div>
              <div style="font-size: 11px; color: #64748b;">{{ $row->itemBkm->nama_item ?? '-' }}</div>
            </td>
            <td style="font-weight: 700; color: #1e293b;">{{ $row->uraian }}</td>
            <td style="text-align: right; font-weight: 700; color: #16a34a; white-space: nowrap;">
              {{ $row->debet ? '+Rp ' . number_format($row->debet, 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; font-weight: 700; color: #dc2626; white-space: nowrap;">
              {{ $row->kredit ? '-Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; white-space: nowrap;">
              @php $isDebit = $row->jenis_transaksi === 'debit'; @endphp
              <div style="font-weight: 800; color: {{ $row->saldo_kas < 0 ? '#dc2626' : '#1e293b' }}; font-size: 13px;">
                {{ $row->saldo_kas < 0 ? '-Rp ' . number_format(abs($row->saldo_kas), 0, ',', '.') : 'Rp ' . number_format($row->saldo_kas, 0, ',', '.') }}
              </div>
              <div style="font-size: 10px; margin-top: 2px; color: {{ $isDebit ? '#16a34a' : '#dc2626' }}; font-weight: 600;">
                {{ $isDebit ? '▲ Masuk' : '▼ Keluar' }}
              </div>
            </td>
            <td style="font-size: 12px; color: #64748b;" title="{{ $row->keterangan }}">
              {{ $row->keterangan ?: '-' }}
            </td>
            <td style="text-align: center; white-space: nowrap;">
              <div style="display: flex; items-center; justify-content: center; gap: 8px;">
                <a href="{{ route('kas_bkm.edit', $row->id) }}" class="icon-btn-edit" title="Edit Transaksi">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <button type="button" class="icon-btn-delete" title="Hapus Transaksi"
                        data-url="{{ route('kas_bkm.destroy', $row->id) }}"
                        data-title="Hapus Transaksi Kas BKM?"
                        data-name="{{ $row->uraian }} ({{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }})"
                        data-type="transaksi Kas BKM"
                        onclick="confirmDelete(this)">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11" style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 500;">
              Belum ada data transaksi Kas BKM.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($transaksis->hasPages())
    <div style="padding: 16px 24px; border-t: 1px solid #e2e8f0;">
      {{ $transaksis->links() }}
    </div>
  @endif
</div>
@endsection
