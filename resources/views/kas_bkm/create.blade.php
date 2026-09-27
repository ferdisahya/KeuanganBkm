@extends('layouts.app')
@section('title', 'Tambah Kas BKM')
@section('topbar-title', 'Input Kas BKM')
@section('topbar-subtitle', 'Tambah entri transaksi Kas Masuk / Kas Keluar BKM')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card {
    position: relative;
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border-radius: 16px;
    padding: 32px 40px;
    margin-bottom: 24px;
    color: #ffffff;
  }
  .dashboard-welcome-card h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 28px;
    font-weight: 800;
    margin: 4px 0;
  }
  .form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
  }
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
  }
  .form-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
  }
  .form-group {
    display: flex;
    flex-direction: column;
    text-align: left;
  }
  .form-group.full-width { grid-column: span 2; }
  .form-label {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #334155;
    margin-bottom: 8px;
  }
  .form-control {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13.5px;
    color: #1e293b;
    font-weight: 500;
    outline: none;
    transition: all 0.2s ease;
  }
  .form-control:focus {
    border-color: #7a0f1d;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(122, 15, 29, 0.08);
  }
  .btn-primary-maroon {
    background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%);
    color: #ffffff;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2);
  }
  .btn-secondary {
    background: #f1f5f9;
    color: #475569;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 14px;
    text-decoration: none;
    border: none;
  }
</style>

<div class="dashboard-welcome-card">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <p style="font-size:13px; opacity:0.8;">Buku Kas Utama BKM</p>
      <h1>Input Kas BKM Baru</h1>
      <p style="font-size:13.5px; opacity:0.8;">Isi data transaksi sesuai bukti fisik / penerimaan & pengeluaran kas</p>
    </div>
    <a href="{{ route('kas_bkm.index') }}" class="btn-secondary" style="background:rgba(255,255,255,0.15); color:#fff;">← Kembali</a>
  </div>
</div>

<div class="form-card">
  <form action="{{ route('kas_bkm.store') }}" method="POST">
    @csrf

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Tanggal Transaksi <span style="color:#dc2626;">*</span></label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $today) }}" required class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Jenis Transaksi <span style="color:#dc2626;">*</span></label>
        <select name="jenis_transaksi" required class="form-control">
          <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Penerimaan / Kas Masuk)</option>
          <option value="kredit" {{ old('jenis_transaksi') == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran / Kas Keluar)</option>
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Uraian Transaksi <span style="color:#dc2626;">*</span></label>
      <input type="text" name="uraian" value="{{ old('uraian') }}" required placeholder="Contoh: Penerimaan Termin I Proyek BKM" class="form-control">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Nominal Transaksi (Rp) <span style="color:#dc2626;">*</span></label>
      <input type="text" name="nominal" value="{{ old('nominal') }}" required placeholder="0" class="form-control rupiah-input" style="font-size:16px; font-weight:700;" oninput="formatRupiahInput(this)">
      <small style="color:#64748b; font-size:12px; margin-top:6px; display:block;">
        💡 Saldo akan dihitung otomatis oleh sistem berdasarkan saldo transaksi sebelumnya
      </small>
    </div>

    <div class="form-grid-3">
      <div class="form-group">
        <label class="form-label">Rekening BKM <span style="color:#dc2626;">*</span></label>
        <select name="rekening_bkm_id" required class="form-control">
          <option value="">-- Pilih Rekening --</option>
          @foreach($rekenings as $r)
            <option value="{{ $r->id }}" {{ old('rekening_bkm_id') == $r->id ? 'selected' : '' }}>
              {{ $r->nomer_rekening }} - {{ $r->nama_rekening }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Subrekening BKM <span style="color:#dc2626;">*</span></label>
        <select name="subrekening_bkm_id" required class="form-control">
          <option value="">-- Pilih Subrekening --</option>
          @foreach($subrekenings as $sr)
            <option value="{{ $sr->id }}" {{ old('subrekening_bkm_id') == $sr->id ? 'selected' : '' }}>
              {{ $sr->nomer_subrekening }} - {{ $sr->nama_subrekening }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Item BKM <span style="color:#dc2626;">*</span></label>
        <select name="item_bkm_id" required class="form-control">
          <option value="">-- Pilih Item --</option>
          @foreach($items as $itm)
            <option value="{{ $itm->id }}" {{ old('item_bkm_id') == $itm->id ? 'selected' : '' }}>
              {{ $itm->nomer_item }} - {{ $itm->nama_item }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
      <label class="form-label">Keterangan / Sumber Dana (Siapa)</label>
      <textarea name="keterangan" rows="3" placeholder="Informasi mengenai sumber dana atau catatan transaksi..." class="form-control">{{ old('keterangan') }}</textarea>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
      <a href="{{ route('kas_bkm.index') }}" class="btn-secondary">Batal</a>
      <button type="submit" class="btn-primary-maroon">Simpan Transaksi Kas BKM</button>
    </div>

  </form>
</div>
@endsection
