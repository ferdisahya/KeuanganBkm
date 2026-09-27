@extends('layouts.app')
@section('title', 'Tambah Sisa Anggaran BKM')
@section('topbar-title', 'Input Sisa Anggaran')
@section('topbar-subtitle', 'Tambah data sisa anggaran / efisiensi BKM')

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; }
  .form-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); }
  .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
  .form-group { display: flex; flex-direction: column; text-align: left; }
  .form-label { font-size: 12.5px; font-weight: 700; text-transform: uppercase; color: #334155; margin-bottom: 8px; }
  .form-control { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 13.5px; color: #1e293b; outline: none; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; }
  .btn-secondary { background: #f1f5f9; color: #475569; font-weight: 600; padding: 12px 20px; border-radius: 10px; text-decoration: none; }
</style>

<div class="dashboard-welcome-card">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <p style="font-size:13px; opacity:0.8;">Efisiensi BKM</p>
      <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Input Sisa Anggaran Baru</h1>
    </div>
    <a href="{{ route('sisa_anggaran.index') }}" class="btn-secondary" style="background:rgba(255,255,255,0.15); color:#fff;">← Kembali</a>
  </div>
</div>

<div class="form-card">
  <form action="{{ route('sisa_anggaran.store') }}" method="POST">
    @csrf

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Nama Anggaran</label>
      <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Sisa Anggaran Belanja Solar Generator" class="form-control">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Harga / Nominal (Rp)</label>
      <input type="text" name="harga" value="{{ old('harga') }}" required placeholder="0" class="form-control rupiah-input" style="font-size:16px; font-weight:700;" oninput="formatRupiahInput(this)">
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
      <label class="form-label">Keterangan (Opsional)</label>
      <textarea name="keterangan" rows="3" placeholder="Catatan tambahan mengenai sisa anggaran..." class="form-control">{{ old('keterangan') }}</textarea>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
      <a href="{{ route('sisa_anggaran.index') }}" class="btn-secondary">Batal</a>
      <button type="submit" class="btn-primary-maroon">Simpan Data Sisa Anggaran</button>
    </div>

  </form>
</div>
@endsection
