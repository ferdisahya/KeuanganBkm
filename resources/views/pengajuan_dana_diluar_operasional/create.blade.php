@extends('layouts.app')
@section('title', 'Tambah Pengajuan Dana Diluar Operasional')
@section('topbar-title', 'Tambah Pengajuan Dana Diluar Operasional')
@section('topbar-subtitle', 'Input data pengajuan dana khusus diluar operasional baru')

@section('content')
<style>
  .form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    max-width: 680px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
  }
  .form-group { margin-bottom: 20px; }
  .form-label { display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
  .form-control-custom {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13.5px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
  }
  .form-control-custom:focus {
    border-color: #7a0f1d;
    background: #ffffff;
  }
  .btn-submit {
    background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%);
    color: #ffffff;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2);
  }
  .btn-cancel {
    background: #f1f5f9;
    color: #64748b;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
  }
</style>

<div class="form-card">
  <h2 style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: #7a0f1d; margin-bottom: 24px;">Tambah Pengajuan Dana Diluar Operasional</h2>

  <form action="{{ route('pengajuan_dana_diluar_operasional.store') }}" method="POST">
    @csrf

    <div class="form-group">
      <label class="form-label">Tanggal Transaksi <span style="color: #dc2626;">*</span></label>
      <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="form-control-custom" required>
    </div>

    <div class="form-group">
      <label class="form-label">Uraian / Deskripsi Pengajuan <span style="color: #dc2626;">*</span></label>
      <textarea name="uraian" rows="3" class="form-control-custom" placeholder="Contoh: Pengajuan kebutuhan khusus diluar operasional..." required>{{ old('uraian') }}</textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Total Nominal (Rp) <span style="color: #dc2626;">*</span></label>
      <input type="text" name="total" value="{{ old('total') }}" placeholder="Masukkan jumlah nominal angka..." class="form-control-custom rupiah-input" required oninput="formatRupiahInput(this)">
    </div>

    <div style="display: flex; gap: 12px; margin-top: 28px;">
      <button type="submit" class="btn-submit">Simpan Pengajuan</button>
      <a href="{{ route('pengajuan_dana_diluar_operasional.index') }}" class="btn-cancel">Batal</a>
    </div>
  </form>
</div>
@endsection
