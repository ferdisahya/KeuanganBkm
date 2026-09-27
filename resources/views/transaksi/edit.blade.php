@extends('layouts.app')

@section('topbar-title', 'Edit Transaksi')

@section('content')
<div class="content-header">
  <div>
    <h2 class="content-title">Ubah Transaksi</h2>
    <p class="content-subtitle">Mengubah data transaksi yang sudah tersimpan.</p>
  </div>
  <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
    Kembali
  </a>
</div>

<div class="card" style="max-width: 600px;">
  @php
    $tanggalVal = $transaksi->tanggal ? \Carbon\Carbon::parse($transaksi->tanggal)->format('Y-m-d') : '';
    $nominalVal = (float)($transaksi->nominal ?? 0);
  @endphp
  <form action="{{ route('transaksi.update', $transaksi) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="form-group">
      <label class="form-label">Tanggal Transaksi</label>
      <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $tanggalVal) }}" required>
      @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Jenis Transaksi</label>
      <select name="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
        <option value="">-- Pilih Jenis --</option>
        <option value="pemasukan" {{ old('jenis', $transaksi->jenis) == 'pemasukan' ? 'selected' : '' }}>Pemasukan (+)</option>
        <option value="pengeluaran" {{ old('jenis', $transaksi->jenis) == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (-)</option>
      </select>
      @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Keterangan / Uraian</label>
      <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', $transaksi->keterangan) }}" placeholder="Misal: Pembayaran Listrik" required>
      @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Nominal (Rp)</label>
      <input type="text" id="nominal_input" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $nominalVal > 0 ? number_format($nominalVal, 0, '', ',') : '') }}" placeholder="0" required>
      @error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div style="margin-top: 24px; display:flex; gap:12px">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>

<script>
  // Format mata uang saat mengetik
  const nominalInput = document.getElementById('nominal_input');
  
  nominalInput.addEventListener('keyup', function(e) {
    let cursorPostion = this.selectionStart;
    let originalLen = this.value.length;
    
    // Hapus semua selain angka
    let val = this.value.replace(/[^0-9]/g, '');
    
    if (val !== '') {
      val = parseInt(val, 10).toLocaleString('en-US');
    }
    
    this.value = val;
    
    // Kembalikan posisi kursor
    let newLen = this.value.length;
    cursorPostion = cursorPostion - originalLen + newLen;
    this.setSelectionRange(cursorPostion, cursorPostion);
  });
</script>
@endsection
