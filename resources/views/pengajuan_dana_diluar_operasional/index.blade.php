@extends('layouts.app')
@section('title', 'Pengajuan Dana Diluar Operasional')
@section('topbar-title', 'Pengajuan Dana Diluar Operasional')
@section('topbar-subtitle', 'Pencatatan & Alokasi Pengajuan Dana Diluar Operasional')
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
    padding: 32px 36px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(122, 15, 29, 0.05);
    color: #ffffff;
  }
  .dashboard-welcome-text h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 28px;
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
  .dashboard-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.01);
  }
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
    color: #2563eb;
    flex-shrink: 0;
  }
  .stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
  .stat-value { font-size: 22px; font-weight: 800; color: #2563eb; font-family: 'Outfit', sans-serif; margin-top: 2px; }

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
    border: none;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2);
  }
  .btn-primary-maroon:hover { color: #ffffff; opacity: 0.95; }
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
    <p>Menu Utama</p>
    <h1>Pengajuan Dana Diluar Operasional</h1>
    <p>Pencatatan draf dan realisasi pengajuan dana khusus diluar operasional</p>
  </div>
  <div style="display: flex; gap: 10px; z-index: 2;">
    <button type="button" class="btn-primary-maroon" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);" onclick="openPdfModal()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
      Simpan / Cetak PDF
    </button>
    <a href="{{ route('pengajuan_dana_diluar_operasional.create') }}" class="btn-primary-maroon">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Pengajuan
    </a>
  </div>
</div>

<div class="dashboard-stat-card">
  <div class="stat-icon">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
  </div>
  <div>
    <div class="stat-label">Grand Total Pengajuan Diluar Operasional</div>
    <div class="stat-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
  </div>
</div>

<!-- Filter & Search Form -->
<div class="filter-card">
  <form action="{{ route('pengajuan_dana_diluar_operasional.index') }}" method="GET">
    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
      <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; flex: 1;">
        <!-- Filter Tanggal Tertentu -->
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control-custom" style="width: 160px;" title="Filter Tanggal Spesifik">
        
        <!-- Filter Bulan -->
        <select name="bulan" class="form-control-custom" style="width: 140px;">
          <option value="">-- Semua Bulan --</option>
          @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $mNum => $mName)
            <option value="{{ $mNum }}" {{ (string)request('bulan') === (string)$mNum ? 'selected' : '' }}>{{ $mName }}</option>
          @endforeach
        </select>

        <!-- Filter Tahun -->
        <select name="tahun" class="form-control-custom" style="width: 120px;">
          <option value="">-- Semua Tahun --</option>
          @for($y = date('Y'); $y >= 2020; $y--)
            <option value="{{ $y }}" {{ (string)request('tahun') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>

        <button type="submit" class="btn-primary-maroon" style="padding: 10px 16px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Filter
        </button>

        @if(request()->hasAny(['tanggal', 'bulan', 'tahun', 'search']))
          <a href="{{ route('pengajuan_dana_diluar_operasional.index') }}" class="btn-primary-maroon" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; box-shadow: none; padding: 10px 14px;" title="Reset Filter">
            Reset
          </a>
        @endif
      </div>

      <!-- Search Box -->
      <div style="position: relative; width: 260px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari uraian transaksi..." class="form-control-custom" style="width: 100%; padding-left: 36px;">
        <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
    </div>
  </form>
</div>

<!-- Table Card -->
<div class="table-card">
  <div style="overflow-x: auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 60px;">No</th>
          <th style="width: 140px;">Tanggal</th>
          <th>Uraian</th>
          <th style="text-align: right; width: 180px;">Total (Rp)</th>
          <th style="text-align: center; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengajuans as $index => $row)
          <tr>
            <td style="font-weight: 600; color: #94a3b8;">{{ $pengajuans->firstItem() + $index }}</td>
            <td style="font-weight: 600; white-space: nowrap;">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
            <td style="font-weight: 600; color: #1e293b;">{{ $row->uraian }}</td>
            <td style="text-align: right; font-weight: 800; color: #2563eb; white-space: nowrap;">
              Rp {{ number_format($row->total, 0, ',', '.') }}
            </td>
            <td style="text-align: center; white-space: nowrap;">
              <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <a href="{{ route('pengajuan_dana_diluar_operasional.edit', $row->id) }}" class="icon-btn-edit" title="Edit">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                </a>
                <button type="button" class="icon-btn-delete" title="Hapus"
                        data-url="{{ route('pengajuan_dana_diluar_operasional.destroy', $row->id) }}"
                        data-title="Hapus Pengajuan Dana Diluar Operasional?"
                        data-name="{{ $row->uraian }} (Rp {{ number_format($row->total, 0, ',', '.') }})"
                        data-type="pengajuan dana diluar operasional"
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
              Belum ada data Pengajuan Dana Diluar Operasional.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($pengajuans->hasPages())
    <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
      {{ $pengajuans->links() }}
    </div>
  @endif
</div>

<!-- Modal Export PDF Popup Overlay -->
<div id="pdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(3px); z-index: 999999; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%); color: #ffffff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
      <h5 style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
        Simpan / Cetak PDF Pengajuan Diluar Operasional
      </h5>
      <button type="button" onclick="closePdfModal()" style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; font-weight: bold;">✕</button>
    </div>
    <form action="{{ route('pengajuan_dana_diluar_operasional.pdf') }}" method="GET" target="_blank" onsubmit="closePdfModal()">
      <div style="padding: 24px; max-height: calc(100vh - 200px); overflow-y: auto;">
        <div>
          <label style="font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 8px; display: block;">Pilih Periode Data yang Akan Dicetak</label>
          
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
              <div>
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Dari Tanggal (Mulai)</label>
                <input type="date" name="tanggal_mulai" class="form-control-custom" style="width: 100%;">
              </div>
              <div>
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Sampai Tanggal (Selesai)</label>
                <input type="date" name="tanggal_selesai" class="form-control-custom" style="width: 100%;">
              </div>
            </div>

            <div style="text-align: center; font-size: 11px; color: #94a3b8; font-weight: 700; margin: 12px 0;">--- ATAU PILIH PER BULAN & TAHUN ---</div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
              <div>
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Bulan</label>
                <select name="bulan" class="form-control-custom" style="width: 100%;">
                  <option value="">-- Semua Bulan --</option>
                  @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $mNum => $mName)
                    <option value="{{ $mNum }}">{{ $mName }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label style="font-size: 12px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;">Tahun</label>
                <select name="tahun" class="form-control-custom" style="width: 100%;">
                  <option value="">-- Semua Tahun --</option>
                  @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>
          <div style="font-size: 11px; color: #64748b; margin-top: 6px;">* Kosongkan filter jika ingin mencetak seluruh transaksi pengajuan dana diluar operasional.</div>
        </div>
      </div>
      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" onclick="closePdfModal()" style="background: #ffffff; border: 1.5px solid #cbd5e1; color: #475569; font-weight: 600; padding: 9px 18px; border-radius: 8px; font-size: 13px; cursor: pointer;">Batal</button>
        <button type="submit" class="btn-primary-maroon" style="border-radius: 8px; padding: 9px 20px; font-size: 13px; cursor: pointer;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Buka PDF & Cetak
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openPdfModal() {
    document.getElementById('pdfModal').style.display = 'flex';
  }
  function closePdfModal() {
    document.getElementById('pdfModal').style.display = 'none';
  }
  document.getElementById('pdfModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closePdfModal();
    }
  });
</script>
@endsection
