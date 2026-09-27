@extends('layouts.app')
@section('title', 'Edit Kas BKM')
@section('topbar-title', 'Edit Kas BKM')
@section('topbar-subtitle', 'Perbarui entri transaksi ID #' . $kasBkm->id)

@section('content')
<style>
  .dashboard-welcome-card {
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border-radius: 16px;
    padding: 32px 40px;
    margin-bottom: 24px;
    color: #ffffff;
  }
  .form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
  }
  .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
  .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
  .form-group { display: flex; flex-direction: column; text-align: left; }
  .form-label { font-size: 12.5px; font-weight: 700; text-transform: uppercase; color: #334155; margin-bottom: 8px; }
  .form-control { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 13.5px; color: #1e293b; outline: none; }
  .btn-primary-maroon { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 600; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; }
  .btn-secondary { background: #f1f5f9; color: #475569; font-weight: 600; padding: 12px 20px; border-radius: 10px; text-decoration: none; }
</style>

<div class="dashboard-welcome-card">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <div>
      <p style="font-size:13px; opacity:0.8;">Edit Entri Kas BKM</p>
      <h1 style="font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; margin:4px 0;">Edit Transaksi #{{ $kasBkm->id }}</h1>
    </div>
    <a href="{{ route('kas_bkm.index') }}" class="btn-secondary" style="background:rgba(255,255,255,0.15); color:#fff;">← Kembali</a>
  </div>
</div>

<div class="form-card">
  @php
    $defaultJenis = old('jenis_transaksi', $kasBkm->jenis_transaksi ?? 'debit');
    $nominalVal = old('nominal', $kasBkm->nominal ?? 0);
    $tanggalVal = $kasBkm->tanggal ? \Carbon\Carbon::parse($kasBkm->tanggal)->format('Y-m-d') : '';
  @endphp
  <form action="{{ route('kas_bkm.update', $kasBkm->id) }}" method="POST">
    @csrf @method('PUT')

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Tanggal Transaksi</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $tanggalVal) }}" required class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Jenis Transaksi</label>
        <select name="jenis_transaksi" required class="form-control">
          <option value="debit" {{ $defaultJenis == 'debit' ? 'selected' : '' }}>Debit (Penerimaan / Kas Masuk)</option>
          <option value="kredit" {{ $defaultJenis == 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran / Kas Keluar)</option>
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Uraian Transaksi</label>
      <input type="text" name="uraian" value="{{ old('uraian', $kasBkm->uraian) }}" required class="form-control">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
      <label class="form-label">Nominal (Rp)</label>
      <input type="text" name="nominal" value="{{ old('nominal', $nominalVal > 0 ? number_format($nominalVal, 0, ',', '.') : '') }}" required class="form-control rupiah-input" style="font-size:16px; font-weight:700;" oninput="formatRupiahInput(this)">
      <small style="color:#64748b; font-size:12px; margin-top:6px; display:block;">
        💡 Saldo akan dihitung ulang otomatis oleh sistem setelah perubahan disimpan
      </small>
    </div>

    <div class="form-grid-3">
      <div class="form-group">
        <label class="form-label">Rekening BKM</label>
        <select name="rekening_bkm_id" required class="form-control">
          <option value="">-- Pilih Rekening BKM --</option>
          @foreach($rekenings as $r)
            <option value="{{ $r->id }}" {{ (string)old('rekening_bkm_id', $kasBkm->rekening_bkm_id) === (string)$r->id ? 'selected' : '' }}>
              {{ $r->nomer_rekening }} - {{ $r->nama_rekening }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Subrekening BKM</label>
        <select name="subrekening_bkm_id" required class="form-control">
          <option value="">-- Pilih Subrekening BKM --</option>
          @foreach($subrekenings as $sr)
            <option value="{{ $sr->id }}" {{ (string)old('subrekening_bkm_id', $kasBkm->subrekening_bkm_id) === (string)$sr->id ? 'selected' : '' }}>
              {{ $sr->nomer_subrekening }} - {{ $sr->nama_subrekening }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Item BKM</label>
        <select name="item_bkm_id" required class="form-control">
          <option value="">-- Pilih Item BKM --</option>
          @foreach($items as $itm)
            <option value="{{ $itm->id }}" {{ (string)old('item_bkm_id', $kasBkm->item_bkm_id) === (string)$itm->id ? 'selected' : '' }}>
              {{ $itm->nomer_item }} - {{ $itm->nama_item }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
      <label class="form-label">Keterangan / Sumber Dana</label>
      <textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $kasBkm->keterangan) }}</textarea>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
      <a href="{{ route('kas_bkm.index') }}" class="btn-secondary">Batal</a>
      <button type="submit" class="btn-primary-maroon">Update Transaksi Kas BKM</button>
    </div>

  </form>
</div>
@endsection
