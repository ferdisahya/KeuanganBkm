@extends('layouts.app')
@section('title', 'Tambah Kas RAAB')
@section('topbar-title', 'Input Kas RAAB')
@section('topbar-subtitle', 'Tambah entri transaksi Kas RAAB')

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
      <p style="font-size:13px; opacity:0.8;">Modul RAAB</p>
      <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Input Kas RAAB Baru</h1>
    </div>
    <a href="{{ route('kas_raab.index') }}" class="btn-secondary" style="background:rgba(255,255,255,0.15); color:#fff;">← Kembali</a>
  </div>
</div>

<div class="form-card">
  <form action="{{ route('kas_raab.store') }}" method="POST">
    @csrf

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Tanggal Transaksi</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $today) }}" required class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Jenis Transaksi</label>
        <select name="jenis_transaksi" required class="form-control">
          <option value="debit" {{ old('jenis_transaksi') == 'debit' ? 'selected' : '' }}>Debit (Alokasi / Masuk)</option>
          <option value="kredit" {{ old('jenis_transaksi') == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran RAAB)</option>
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Uraian Transaksi RAAB</label>
      <input type="text" name="uraian" value="{{ old('uraian') }}" required placeholder="Contoh: Alokasi Pembayaran Mandor Minggu II" class="form-control">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Nominal (Rp) <span style="color:#dc2626;">*</span></label>
      <input type="text" name="nominal" value="{{ old('nominal') }}" required placeholder="0" class="form-control rupiah-input" style="font-size:16px; font-weight:700;" oninput="formatRupiahInput(this)">
      <small style="color:#64748b; font-size:12px; margin-top:6px; display:block;">
        💡 Saldo RAAB akan dihitung otomatis oleh sistem berdasarkan saldo transaksi sebelumnya
      </small>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Rekening RAAB</label>
        <select name="rekening_raab_id" required class="form-control">
          <option value="">-- Pilih Rekening RAAB --</option>
          @foreach($rekenings as $r)
            <option value="{{ $r->id }}" {{ old('rekening_raab_id') == $r->id ? 'selected' : '' }}>
              {{ $r->nomer_rekening }} - {{ $r->nama_rekening }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Subrekening RAAB</label>
        <select name="subrekening_raab_id" required class="form-control">
          <option value="">-- Pilih Subrekening RAAB --</option>
          @foreach($subrekenings as $sr)
            <option value="{{ $sr->id }}" {{ old('subrekening_raab_id') == $sr->id ? 'selected' : '' }}>
              {{ $sr->nomer_subrekening }} - {{ $sr->nama_subrekening }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-grid" style="margin-top: 20px;">
      <div class="form-group">
        <label class="form-label">Lokasi (Nomer & Nama Lokasi)</label>
        <select name="nomer_lokasi" class="form-control">
          <option value="">-- Tanpa Lokasi / #N/A --</option>
          @foreach($lokasis as $l)
            <option value="{{ $l->kode }}" {{ old('nomer_lokasi') == $l->kode ? 'selected' : '' }}>
              {{ $l->kode }} - {{ $l->nama }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Supplier / Toko (No Supplier & Nama)</label>
        <select name="no_supplier" class="form-control">
          <option value="">-- Tanpa Supplier / #N/A --</option>
          @foreach($tokos as $t)
            <option value="{{ $t->kode }}" {{ old('no_supplier') == $t->kode ? 'selected' : '' }}>
              {{ $t->kode }} - {{ $t->nama }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 24px;">
      <a href="{{ route('kas_raab.index') }}" class="btn-secondary">Batal</a>
      <button type="submit" class="btn-primary-maroon">Simpan Transaksi Kas RAAB</button>
    </div>

  </form>
</div>
@endsection
