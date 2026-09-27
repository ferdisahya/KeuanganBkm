@extends('layouts.app')
@section('title', 'Kas RAAB')
@section('topbar-title', 'Kas RAAB')
@section('topbar-subtitle', 'Pencatatan transaksi Kas RAAB')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card {
    position: relative;
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border-radius: 16px;
    padding: 36px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
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
  .icon-blue { background: #eff6ff; color: #2563eb; }
  .icon-purple { background: #faf5ff; color: #9333ea; }
  .icon-indigo { background: #e0e7ff; color: #3730a3; }
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
  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
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
  }
  .form-control-custom {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
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

<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2;">
    <p>Kas RAAB</p>
    <h1>Kas RAAB</h1>
    <p>Monitoring alokasi dan realisasi anggaran operasional & proyek</p>
  </div>
  <a href="{{ route('kas_raab.create') }}" class="btn-primary-maroon" style="z-index: 2;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Transaksi RAAB
  </a>
</div>

<div class="dashboard-stats-grid">
  <div class="dashboard-stat-card">
    <div class="stat-icon icon-blue">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Debet RAAB</div>
      <div class="stat-value" style="color:#2563eb;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card">
    <div class="stat-icon icon-purple">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Total Kredit RAAB</div>
      <div class="stat-value" style="color:#9333ea;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card">
    <div class="stat-icon icon-indigo" style="{{ $saldoAkhir < 0 ? 'background: #fdf2f2; color: #dc2626;' : '' }}">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
      </svg>
    </div>
    <div>
      <div class="stat-label">Saldo Kas RAAB {{ request()->hasAny(['search', 'tanggal', 'bulan', 'tahun']) ? '(Terfilter)' : '' }}</div>
      <div class="stat-value" style="color: {{ $saldoAkhir < 0 ? '#dc2626' : '#3730a3' }};">
        {{ $saldoAkhir < 0 ? '-Rp ' . number_format(abs($saldoAkhir), 0, ',', '.') : 'Rp ' . number_format($saldoAkhir, 0, ',', '.') }}
      </div>
    </div>
  </div>
</div>

<div class="filter-card">
  <form method="GET" action="{{ route('kas_raab.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari uraian, rekening, subrekening RAAB..." class="form-control-custom" style="width: 100%;">
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
      <a href="{{ route('kas_raab.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; padding: 10px 14px;">Reset</a>
    @endif
  </form>
</div>

<div class="table-card">
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Tanggal</th>
          <th>Rekening RAAB</th>
          <th>Subrekening RAAB</th>
          <th>Uraian</th>
          <th>Lokasi</th>
          <th>Supplier</th>
          <th style="text-align: right;">Debet</th>
          <th style="text-align: right;">Kredit</th>
          <th style="text-align: right;">Saldo</th>
          <th style="text-align: center; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksis as $index => $row)
          <tr>
            <td style="font-weight: 600; color: #94a3b8;">{{ $transaksis->firstItem() + $index }}</td>
            <td style="font-weight: 600; white-space: nowrap;">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->rekeningRaab->nomer_rekening ?? '-' }}</div>
              <div style="font-size: 11px; color: #64748b;">{{ $row->rekeningRaab->nama_rekening ?? '-' }}</div>
            </td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->subrekeningRaab->nomer_subrekening ?? '-' }}</div>
              <div style="font-size: 11px; color: #64748b;">{{ $row->subrekeningRaab->nama_subrekening ?? '-' }}</div>
            </td>
            <td style="font-weight: 700; color: #1e293b;">{{ $row->uraian }}</td>
            <td>
              @if($row->lokasi || $row->nomer_lokasi)
                <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->nomer_lokasi ?? ($row->lokasi->kode ?? '-') }}</div>
                <div style="font-size: 11px; color: #64748b;">{{ $row->lokasi->nama ?? '-' }}</div>
              @else
                <span style="color: #94a3b8; font-size: 12px;">-</span>
              @endif
            </td>
            <td>
              @if($row->toko || $row->no_supplier)
                <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $row->no_supplier ?? ($row->toko->kode ?? '-') }}</div>
                <div style="font-size: 11px; color: #64748b;">{{ $row->toko->nama ?? '-' }}</div>
              @else
                <span style="color: #94a3b8; font-size: 12px;">-</span>
              @endif
            </td>
            <td style="text-align: right; font-weight: 700; color: #2563eb; white-space: nowrap;">
              {{ $row->debet ? '+Rp ' . number_format($row->debet, 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; font-weight: 700; color: #9333ea; white-space: nowrap;">
              {{ $row->kredit ? '-Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}
            </td>
            <td style="text-align: right; white-space: nowrap;">
              @php $isDebit = $row->jenis_transaksi === 'debit'; @endphp
              <div style="font-weight: 800; color: {{ $row->saldo < 0 ? '#dc2626' : '#1e293b' }}; font-size: 13px;">
                {{ $row->saldo < 0 ? '-Rp ' . number_format(abs($row->saldo), 0, ',', '.') : 'Rp ' . number_format($row->saldo, 0, ',', '.') }}
              </div>
              <div style="font-size: 10px; margin-top: 2px; color: {{ $isDebit ? '#2563eb' : '#9333ea' }}; font-weight: 600;">
                {{ $isDebit ? '▲ Masuk' : '▼ Keluar' }}
              </div>
            </td>
            <td style="text-align: center; white-space: nowrap;">
              <div style="display: flex; items-center; justify-content: center; gap: 8px;">
                <a href="{{ route('kas_raab.edit', $row->id) }}" class="icon-btn-edit" title="Edit Transaksi">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <button type="button" class="icon-btn-delete" title="Hapus Transaksi"
                        data-url="{{ route('kas_raab.destroy', $row->id) }}"
                        data-title="Hapus Transaksi RAAB?"
                        data-name="{{ $row->uraian }} ({{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }})"
                        data-type="transaksi Kas RAAB"
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
              Belum ada data transaksi Kas RAAB.
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
