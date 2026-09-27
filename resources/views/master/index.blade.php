@extends('layouts.app')
@section('title', 'Master Data')
@section('topbar-title', 'Master Data')
@section('topbar-subtitle', 'Kelola Kode Rekening, Subrekening BKM/RAAB, dan Item BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <ellipse cx="12" cy="5" rx="9" ry="3"/>
    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
    <path d="M21 19c0 1.66-4 3-9 3s-9-1.34-9-3"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; }
  .master-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
  .master-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .master-title { font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; justify-content: space-between; }
  .form-control-sm { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #1e293b; outline: none; }
  .btn-add { background: #1e293b; color: #ffffff; font-weight: 600; padding: 8px 16px; border-radius: 8px; border: none; font-size: 12.5px; cursor: pointer; }
  .mini-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 14px; }
  .mini-table th { background: #f8fafc; padding: 10px 12px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
  .mini-table td { padding: 10px 12px; font-size: 12.5px; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
  .icon-btn-delete-sm { width: 28px; height: 28px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); color: #dc2626; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; cursor: pointer; }
  .icon-btn-delete-sm:hover { background: #dc2626; color: #ffffff; }
</style>

<div class="dashboard-welcome-card">
  <div style="z-index: 2;">
    <p style="font-size:13px; opacity:0.8; font-weight:500;">Pengaturan Referensi</p>
    <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Kelola Master Data BKM & RAAB</h1>
    <p style="font-size:13.5px; opacity:0.8; font-weight:500;">Atur daftar Kode Rekening, Subrekening, dan Item penomoran transaksi BKM & RAAB</p>
  </div>
</div>



<div class="master-grid">

  {{-- Rekening BKM --}}
  <div class="master-card">
    <div class="master-title">
      <span>Rekening BKM</span>
      <span style="font-size: 11px; font-weight: 600; color: #94a3b8; font-family: monospace;">rekening_bkm</span>
    </div>

    <form action="{{ route('master.rekening_bkm.store') }}" method="POST" style="display: flex; gap: 8px;">
      @csrf
      <input type="text" name="nomer_rekening" placeholder="No. Rek (110-01)" required class="form-control-sm" style="width: 35%;">
      <input type="text" name="nama_rekening" placeholder="Nama Rekening BKM" required class="form-control-sm" style="flex: 1;">
      <button type="submit" class="btn-add">Tambah</button>
    </form>

    <table class="mini-table">
      <thead>
        <tr>
          <th>No. Rekening</th>
          <th>Nama Rekening</th>
          <th style="text-align: center; width: 60px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rekeningBkms as $row)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #7a0f1d;">{{ $row->nomer_rekening }}</td>
            <td style="font-weight: 600;">{{ $row->nama_rekening }}</td>
            <td style="text-align: center;">
              <button type="button" class="icon-btn-delete-sm" title="Hapus Rekening"
                      data-url="{{ route('master.rekening_bkm.destroy', $row->id) }}"
                      data-title="Hapus Rekening BKM?"
                      data-name="{{ $row->nomer_rekening }} - {{ $row->nama_rekening }}"
                      data-type="rekening BKM"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Rekening RAAB --}}
  <div class="master-card">
    <div class="master-title">
      <span>Rekening RAAB</span>
      <span style="font-size: 11px; font-weight: 600; color: #94a3b8; font-family: monospace;">rekening_raab</span>
    </div>

    <form action="{{ route('master.rekening_raab.store') }}" method="POST" style="display: flex; gap: 8px;">
      @csrf
      <input type="text" name="nomer_rekening" placeholder="No. Rek (210-01)" required class="form-control-sm" style="width: 35%;">
      <input type="text" name="nama_rekening" placeholder="Nama Rekening RAAB" required class="form-control-sm" style="flex: 1;">
      <button type="submit" class="btn-add">Tambah</button>
    </form>

    <table class="mini-table">
      <thead>
        <tr>
          <th>No. Rekening</th>
          <th>Nama Rekening</th>
          <th style="text-align: center; width: 60px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rekeningRaabs as $row)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #2563eb;">{{ $row->nomer_rekening }}</td>
            <td style="font-weight: 600;">{{ $row->nama_rekening }}</td>
            <td style="text-align: center;">
              <button type="button" class="icon-btn-delete-sm" title="Hapus Rekening"
                      data-url="{{ route('master.rekening_raab.destroy', $row->id) }}"
                      data-title="Hapus Rekening RAAB?"
                      data-name="{{ $row->nomer_rekening }} - {{ $row->nama_rekening }}"
                      data-type="rekening RAAB"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Subrekening BKM --}}
  <div class="master-card">
    <div class="master-title">
      <span>Subrekening BKM</span>
      <span style="font-size: 11px; font-weight: 600; color: #94a3b8; font-family: monospace;">subrekening_bkm</span>
    </div>

    <form action="{{ route('master.subrekening_bkm.store') }}" method="POST" style="display: flex; gap: 8px;">
      @csrf
      <input type="text" name="nomer_subrekening" placeholder="No. Sub (110-01-01)" required class="form-control-sm" style="width: 38%;">
      <input type="text" name="nama_subrekening" placeholder="Nama Subrekening" required class="form-control-sm" style="flex: 1;">
      <button type="submit" class="btn-add">Tambah</button>
    </form>

    <table class="mini-table">
      <thead>
        <tr>
          <th>No. Subrekening</th>
          <th>Nama Subrekening</th>
          <th style="text-align: center; width: 60px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subrekeningBkms as $row)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #16a34a;">{{ $row->nomer_subrekening }}</td>
            <td style="font-weight: 600;">{{ $row->nama_subrekening }}</td>
            <td style="text-align: center;">
              <button type="button" class="icon-btn-delete-sm" title="Hapus Subrekening"
                      data-url="{{ route('master.subrekening_bkm.destroy', $row->id) }}"
                      data-title="Hapus Subrekening BKM?"
                      data-name="{{ $row->nomer_subrekening }} - {{ $row->nama_subrekening }}"
                      data-type="subrekening BKM"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Subrekening RAAB --}}
  <div class="master-card">
    <div class="master-title">
      <span>Subrekening RAAB</span>
      <span style="font-size: 11px; font-weight: 600; color: #94a3b8; font-family: monospace;">subrekening_raab</span>
    </div>

    <form action="{{ route('master.subrekening_raab.store') }}" method="POST" style="display: flex; gap: 8px;">
      @csrf
      <input type="text" name="nomer_subrekening" placeholder="No. Sub (210-01-01)" required class="form-control-sm" style="width: 38%;">
      <input type="text" name="nama_subrekening" placeholder="Nama Subrekening" required class="form-control-sm" style="flex: 1;">
      <button type="submit" class="btn-add">Tambah</button>
    </form>

    <table class="mini-table">
      <thead>
        <tr>
          <th>No. Subrekening</th>
          <th>Nama Subrekening</th>
          <th style="text-align: center; width: 60px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subrekeningRaabs as $row)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #9333ea;">{{ $row->nomer_subrekening }}</td>
            <td style="font-weight: 600;">{{ $row->nama_subrekening }}</td>
            <td style="text-align: center;">
              <button type="button" class="icon-btn-delete-sm" title="Hapus Subrekening"
                      data-url="{{ route('master.subrekening_raab.destroy', $row->id) }}"
                      data-title="Hapus Subrekening RAAB?"
                      data-name="{{ $row->nomer_subrekening }} - {{ $row->nama_subrekening }}"
                      data-type="subrekening RAAB"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Item BKM --}}
  <div class="master-card" style="grid-column: span 2;">
    <div class="master-title">
      <span>Item BKM</span>
      <span style="font-size: 11px; font-weight: 600; color: #94a3b8; font-family: monospace;">item_bkm</span>
    </div>

    <form action="{{ route('master.item_bkm.store') }}" method="POST" style="display: flex; gap: 8px; max-width: 600px;">
      @csrf
      <input type="text" name="nomer_item" placeholder="No. Item (ITM-001)" required class="form-control-sm" style="width: 35%;">
      <input type="text" name="nama_item" placeholder="Nama Item BKM" required class="form-control-sm" style="flex: 1;">
      <button type="submit" class="btn-add">Tambah Item BKM</button>
    </form>

    <table class="mini-table">
      <thead>
        <tr>
          <th>No. Item</th>
          <th>Nama Item BKM</th>
          <th style="text-align: center; width: 60px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($itemBkms as $row)
          <tr>
            <td style="font-family: monospace; font-weight: 700; color: #b45309;">{{ $row->nomer_item }}</td>
            <td style="font-weight: 600;">{{ $row->nama_item }}</td>
            <td style="text-align: center;">
              <button type="button" class="icon-btn-delete-sm" title="Hapus Item"
                      data-url="{{ route('master.item_bkm.destroy', $row->id) }}"
                      data-title="Hapus Item BKM?"
                      data-name="{{ $row->nomer_item }} - {{ $row->nama_item }}"
                      data-type="item BKM"
                      onclick="confirmDelete(this)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>
@endsection
