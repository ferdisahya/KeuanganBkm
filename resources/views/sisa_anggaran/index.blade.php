@extends('layouts.app')
@section('title', 'Sisa Anggaran BKM')
@section('topbar-title', 'Sisa Anggaran BKM')
@section('topbar-subtitle', 'Daftar sisa anggaran efisiensi & pengembalian BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%); border-radius: 16px; padding: 36px 40px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; color: #ffffff; }
  .dashboard-welcome-text h1 { font-family: 'Outfit', sans-serif; font-size: 30px; font-weight: 800; color: #ffffff; margin: 4px 0; }
  .stat-card-single { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; }
  .filter-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; }
  .table-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .data-table th { background: #f8fafc; padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; border-bottom: 1px solid #e2e8f0; }
  .data-table td { padding: 14px 18px; font-size: 13px; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 10px 20px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; border: none; cursor: pointer; }
  .form-control-custom { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13px; color: #1e293b; outline: none; }
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
  <div class="dashboard-welcome-text">
    <p style="font-size:13px; opacity:0.8; font-weight:500;">Efisiensi & Anggaran</p>
    <h1>Sisa Anggaran BKM</h1>
    <p style="font-size:13.5px; opacity:0.8; font-weight:500;">Daftar alokasi sisa anggaran dan pengembalian efisiensi BKM</p>
  </div>
  <a href="{{ route('sisa_anggaran.create') }}" class="btn-primary-maroon">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Sisa Anggaran
  </a>
</div>

<div class="stat-card-single">
  <div style="width:48px; height:48px; border-radius:12px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
    </svg>
  </div>
  <div>
    <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#64748b;">Total Sisa Anggaran BKM</div>
    <div style="font-size:24px; font-weight:800; color:#1e293b; font-family:'Outfit',sans-serif; margin-top:2px;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
  </div>
</div>

<div class="filter-card">
  <form method="GET" action="{{ route('sisa_anggaran.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau keterangan sisa anggaran..." class="form-control-custom" style="width: 100%;">
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
      <a href="{{ route('sisa_anggaran.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px; font-weight: 600; padding: 10px 14px;">Reset</a>
    @endif
  </form>
</div>

<div class="table-card">
  <div style="overflow-x:auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Nama Anggaran</th>
          <th style="text-align: right;">Harga / Nominal (Rp)</th>
          <th>Keterangan</th>
          <th style="text-align: center; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $index => $row)
          <tr>
            <td style="font-weight: 600; color: #94a3b8;">{{ $items->firstItem() + $index }}</td>
            <td style="font-weight: 700; color: #1e293b;">{{ $row->nama }}</td>
            <td style="text-align: right; font-weight: 800; color: #16a34a; white-space: nowrap;">
              Rp {{ number_format($row->harga, 0, ',', '.') }}
            </td>
            <td style="font-size: 12px; color: #64748b;" title="{{ $row->keterangan }}">
              {{ $row->keterangan ?: '-' }}
            </td>
            <td style="text-align: center; white-space: nowrap;">
              <div style="display: flex; items-center; justify-content: center; gap: 8px;">
                <a href="{{ route('sisa_anggaran.edit', $row->id) }}" class="icon-btn-edit" title="Edit Data">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <button type="button" class="icon-btn-delete" title="Hapus Data"
                        data-url="{{ route('sisa_anggaran.destroy', $row->id) }}"
                        data-title="Hapus Sisa Anggaran BKM?"
                        data-name="{{ $row->nama }} (Rp {{ number_format($row->harga, 0, ',', '.') }})"
                        data-type="sisa anggaran BKM"
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
            <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 500;">
              Belum ada data Sisa Anggaran BKM.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($items->hasPages())
    <div style="padding: 16px 24px; border-t: 1px solid #e2e8f0;">
      {{ $items->links() }}
    </div>
  @endif
</div>
@endsection
