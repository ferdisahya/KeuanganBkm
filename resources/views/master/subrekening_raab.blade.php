@extends('layouts.app')
@section('title', 'Master Subrekening RAAB')
@section('topbar-title', 'Master Subrekening RAAB')
@section('topbar-subtitle', 'Kelola daftar nomor dan nama Subrekening RAAB')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; border: 1px solid rgba(223, 187, 109, 0.3); box-shadow: 0 8px 24px rgba(122, 15, 29, 0.12); position: relative; }
  .dashboard-welcome-card::after { content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 100%; background: radial-gradient(circle, rgba(223, 187, 109, 0.15) 0%, transparent 70%); pointer-events: none; }
  .form-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .table-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .data-table th { background: #f8fafc; padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569; border-bottom: 1px solid #e2e8f0; }
  .data-table td { padding: 14px 18px; font-size: 13px; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
  .form-control-custom { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13.5px; color: #1e293b; outline: none; transition: border-color 0.2s; }
  .form-control-custom:focus { border-color: #dfbb6d; background: #ffffff; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(223, 187, 109, 0.4); cursor: pointer; font-size: 13.5px; box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2); transition: all 0.2s; }
  .btn-primary-maroon:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(122, 15, 29, 0.3); }
  .icon-btn-edit { width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); color: #2563eb; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; cursor: pointer; }
  .icon-btn-edit:hover { background: #2563eb; color: #ffffff; }
  .icon-btn-delete { width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #dc2626; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; cursor: pointer; }
  .icon-btn-delete:hover { background: #dc2626; color: #ffffff; }
</style>

<div class="dashboard-welcome-card">
  <div>
    <span style="color:#dfbb6d; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">Master Data RAAB</span>
    <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Subrekening RAAB</h1>
    <p style="font-size:13.5px; opacity:0.85; font-weight:500;">Kelola daftar rincian Subrekening RAAB</p>
  </div>
</div>

<div class="form-card">
  <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0 0 16px 0;">Tambah Subrekening RAAB Baru</h3>
  <form action="{{ route('master.subrekening_raab.store') }}" method="POST" style="display: flex; gap: 12px; flex-wrap: wrap;">
    @csrf
    <div style="width: 240px;">
      <input type="text" name="nomer_subrekening" value="{{ old('nomer_subrekening') }}" placeholder="No. Sub (contoh: 210-01-01)" required class="form-control-custom" style="width:100%;">
    </div>
    <div style="flex: 1; min-width: 260px;">
      <input type="text" name="nama_subrekening" value="{{ old('nama_subrekening') }}" placeholder="Nama Subrekening RAAB" required class="form-control-custom" style="width:100%;">
    </div>
    <button type="submit" class="btn-primary-maroon">Simpan Subrekening</button>
  </form>
</div>

<div class="table-card">
  <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
    <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">Daftar Subrekening RAAB</h3>
    <form method="GET" action="{{ route('master.subrekening_raab.index') }}" style="display:flex; gap:8px;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="form-control-custom" style="padding:6px 12px; font-size:12.5px;">
      <button type="submit" class="btn-primary-maroon" style="padding:6px 14px; font-size:12.5px;">Cari</button>
    </form>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 60px;">No</th>
        <th>Nomer Subrekening</th>
        <th>Nama Subrekening RAAB</th>
        <th style="text-align: center; width: 120px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($subrekenings as $index => $row)
        <tr>
          <td style="font-weight: 600; color: #94a3b8;">{{ $subrekenings->firstItem() + $index }}</td>
          <td style="font-family: monospace; font-weight: 700; color: #9333ea; font-size: 14px;">{{ $row->nomer_subrekening }}</td>
          <td style="font-weight: 700; color: #1e293b;">{{ $row->nama_subrekening }}</td>
          <td style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <button type="button" class="icon-btn-edit"
                      data-id="{{ $row->id }}"
                      data-nomer="{{ $row->nomer_subrekening }}"
                      data-nama="{{ $row->nama_subrekening }}"
                      onclick="openEditModal(this)"
                      title="Edit Subrekening RAAB">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </button>
              <button type="button" class="icon-btn-delete" title="Hapus Subrekening"
                      data-url="{{ route('master.subrekening_raab.destroy', $row->id) }}"
                      data-title="Hapus Subrekening RAAB?"
                      data-name="{{ $row->nomer_subrekening }} - {{ $row->nama_subrekening }}"
                      data-type="subrekening RAAB"
                      onclick="confirmDelete(this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada data Subrekening RAAB.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  @if($subrekenings->hasPages())
    <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">{{ $subrekenings->links() }}</div>
  @endif
</div>

<!-- Modal Edit Subrekening RAAB -->
<div id="editModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:16px; width:100%; max-width:480px; padding:24px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:700; color:#1e293b; margin:0;">Edit Subrekening RAAB</h3>
      <button type="button" onclick="closeEditModal()" style="background:none; border:none; font-size:22px; cursor:pointer; color:#64748b;">&times;</button>
    </div>
    <form id="editForm" method="POST">
      @csrf
      @method('PUT')
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">Nomer Subrekening</label>
        <input type="text" id="edit_nomer" name="nomer_subrekening" required class="form-control-custom" style="width:100%;">
      </div>
      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:6px;">Nama Subrekening RAAB</label>
        <input type="text" id="edit_nama" name="nama_subrekening" required class="form-control-custom" style="width:100%;">
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="closeEditModal()" style="padding:10px 18px; border-radius:8px; border:1px solid #cbd5e1; background:#fff; font-weight:600; font-size:13px; cursor:pointer;">Batal</button>
        <button type="submit" class="btn-primary-maroon" style="padding:10px 18px; font-size:13px;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditModal(btn) {
    const id = btn.dataset.id;
    const nomer = btn.dataset.nomer || '';
    const nama = btn.dataset.nama || '';
    document.getElementById('editForm').action = '/master-data/subrekening-raab/' + id;
    document.getElementById('edit_nomer').value = nomer;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('editModal').style.display = 'flex';
  }
  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }
</script>
@endsection
