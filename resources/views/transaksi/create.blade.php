@extends('layouts.app')
@section('title', 'Input Transaksi')
@section('topbar-title', 'Input Transaksi')
@section('topbar-subtitle', 'Tambahkan catatan transaksi keuangan baru')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
  </svg>
@endsection

@section('content')
<style>
  /* ── Form Card & Page Header Overhaul ── */
  .dashboard-welcome-card {
    position: relative;
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border: none;
    border-radius: 16px;
    padding: 36px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(122, 15, 29, 0.05);
    color: #ffffff;
  }

  .dashboard-welcome-text h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin-top: 4px;
    margin-bottom: 8px;
  }

  .dashboard-welcome-text p {
    font-size: 13.5px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .dashboard-welcome-line {
    width: 40px;
    height: 4px;
    background: #ffffff;
    border-radius: 2px;
    margin-top: 16px;
  }

  .dashboard-watermark-img {
    position: absolute;
    right: 20px;
    bottom: 0;
    height: 100%;
    width: auto;
    max-width: 45%;
    opacity: 0.95;
    z-index: 1;
    pointer-events: none;
    object-fit: contain;
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
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    text-align: left;
  }

  .form-group.full-width {
    grid-column: span 2;
  }

  .form-group.half-width {
    grid-column: span 1;
  }

  .form-label {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
  }

  .form-label .required {
    color: #dc2626;
  }

  .form-control-wrap {
    position: relative;
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

  .form-control.is-invalid {
    border-color: #dc2626;
    background: #fffdfd;
  }

  .form-control.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
  }

  .form-control-wrap .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    display: flex;
    align-items: center;
    pointer-events: none;
  }

  .form-control-wrap .input-icon-text {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #7a0f1d;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 13.5px;
    pointer-events: none;
  }

  .form-control.with-icon {
    padding-left: 44px;
  }

  .form-error {
    font-size: 11px;
    color: #dc2626;
    margin-top: 4px;
    font-weight: 600;
    min-height: 16px;
  }

  .form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #f1f5f9;
    padding-top: 24px;
    margin-top: 24px;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 10px;
    padding: 10px 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: inherit;
  }

  .btn-ghost {
    background: transparent;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
  }

  .btn-ghost:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
  }

  .btn-primary {
    background: #7a0f1d;
    border: 1.5px solid transparent;
    color: #ffffff;
    padding: 10px 24px;
  }

  .btn-primary:hover {
    background: #5c0610;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.15);
  }
</style>

<!-- Welcome Banner Header with Clipboard/Calculator Redesign -->
<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2; position: relative;">
    <p>Kelola Transaksi Keuangan</p>
    <h1>Input Transaksi</h1>
    <p>Tambahkan data pemasukan atau pengeluaran baru berdasarkan bukti transaksi.</p>
    <div class="dashboard-welcome-line"></div>
  </div>
  
  <!-- 3D Isometric Desktop (Clipboard, calculator, coins, plant) -->
  <img class="dashboard-watermark-img" src="{{ asset('images/transaksi_banner.png') }}" alt="Transaksi" />
</div>

<!-- Main Form Card Container -->
<div class="form-card">
  <!-- Card Header inside the container -->
  <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 28px;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(122, 15, 29, 0.06); color: #7a0f1d; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
        <line x1="12" y1="4" x2="12" y2="20"/>
      </svg>
    </div>
    <div style="text-align: left;">
      <h2 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; color: #7a0f1d; margin: 0;">Form Input Transaksi</h2>
      <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">Isi data transaksi dengan lengkap dan benar</p>
    </div>
  </div>

  <form action="{{ route('transaksi.store') }}" method="POST" id="formTransaksi">
    @csrf
    <div class="form-grid">

      {{-- Tanggal --}}
      <div class="form-group">
        <label for="tanggal" class="form-label">Tanggal <span class="required">*</span></label>
        <div class="form-control-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </span>
          <input type="date"
                 id="tanggal"
                 name="tanggal"
                 class="form-control with-icon @error('tanggal') is-invalid @enderror"
                 value="{{ old('tanggal', $today) }}"
                 max="{{ now()->format('Y-m-d') }}"
                 required />
        </div>
        @error('tanggal')
          <div class="form-error">{{ $message }}</div>
        @else
          <div class="form-error"></div>
        @enderror
      </div>

      {{-- Jenis --}}
      <div class="form-group">
        <label for="jenis" class="form-label">Jenis Transaksi <span class="required">*</span></label>
        <select id="jenis" name="jenis"
                class="form-control @error('jenis') is-invalid @enderror" required>
          <option value="">— Pilih Jenis —</option>
          <option value="pemasukan"   {{ old('jenis') === 'pemasukan'   ? 'selected' : '' }}>pemasukan</option>
          <option value="pengeluaran" {{ old('jenis') === 'pengeluaran' ? 'selected' : '' }}>pengeluaran</option>
        </select>
        @error('jenis')
          <div class="form-error">{{ $message }}</div>
        @else
          <div class="form-error"></div>
        @enderror
      </div>

      {{-- Keterangan --}}
      <div class="form-group full-width">
        <label for="keterangan" class="form-label">Keterangan <span class="required">*</span></label>
        <div class="form-control-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
          </span>
          <input type="text"
                 id="keterangan"
                 name="keterangan"
                 class="form-control with-icon @error('keterangan') is-invalid @enderror"
                 placeholder="Contoh: Pembayaran tagihan listrik bulan Juni…"
                 value="{{ old('keterangan') }}"
                 maxlength="255"
                 required />
        </div>
        @error('keterangan')
          <div class="form-error">{{ $message }}</div>
        @else
          <div class="form-error"></div>
        @enderror
      </div>

      {{-- Nominal --}}
      <div class="form-group half-width">
        <label for="nominal_display" class="form-label">Nominal (Rp) <span class="required">*</span></label>
        <div class="form-control-wrap">
          <span class="input-icon-text">Rp</span>
          <input type="text"
                 id="nominal_display"
                 class="form-control with-icon @error('nominal') is-invalid @enderror"
                 placeholder="Masukkan nominal transaksi"
                 inputmode="numeric"
                 autocomplete="off" />
          <input type="hidden" id="nominal" name="nominal" value="{{ old('nominal') }}" />
        </div>
        @error('nominal')
          <div class="form-error">{{ $message }}</div>
        @else
          <div class="form-error"></div>
        @enderror
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px" id="nominalPreview"></div>
      </div>

    </div>{{-- end form-grid --}}

    <!-- Form Action Buttons -->
    <div class="form-actions">
      <a href="{{ route('transaksi.index') }}" class="btn btn-ghost">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
        Batal
      </a>
      <button type="reset" class="btn btn-ghost" onclick="resetForm()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
        </svg>
        Reset
      </button>
      <button type="submit" class="btn btn-primary" id="btnSimpan">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
        </svg>
        Simpan Transaksi
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // Format angka dengan titik ribuan
  const nominalDisplay = document.getElementById('nominal_display');
  const nominalHidden  = document.getElementById('nominal');
  const nominalPreview = document.getElementById('nominalPreview');

  // Jika ada old value
  if (nominalHidden.value) {
    const raw = parseFloat(nominalHidden.value);
    nominalDisplay.value = raw.toLocaleString('id-ID');
    nominalPreview.textContent = 'Terbaca: Rp ' + raw.toLocaleString('id-ID');
  }

  nominalDisplay.addEventListener('input', function () {
    // Hapus semua non-digit
    const raw = this.value.replace(/\D/g, '');
    // Format dengan titik
    if (raw) {
      this.value = parseInt(raw).toLocaleString('id-ID');
      nominalHidden.value = raw;
      nominalPreview.textContent = 'Terbaca: Rp ' + parseInt(raw).toLocaleString('id-ID');
    } else {
      this.value = '';
      nominalHidden.value = '';
      nominalPreview.textContent = '';
    }
  });

  function resetForm() {
    nominalDisplay.value = '';
    nominalHidden.value  = '';
    nominalPreview.textContent = '';
  }

  // Validasi frontend sebelum submit
  document.getElementById('formTransaksi').addEventListener('submit', function(e) {
    let valid = true;

    // Cek tanggal
    const tanggal = document.getElementById('tanggal');
    if (!tanggal.value) {
      tanggal.classList.add('is-invalid');
      tanggal.closest('.form-control-wrap').nextElementSibling.textContent = 'Tanggal wajib diisi.';
      valid = false;
    }

    // Cek jenis
    const jenis = document.getElementById('jenis');
    if (!jenis.value) {
      jenis.classList.add('is-invalid');
      jenis.nextElementSibling.textContent = 'Jenis transaksi wajib dipilih.';
      valid = false;
    }

    // Cek keterangan
    const ket = document.getElementById('keterangan');
    if (!ket.value.trim()) {
      ket.classList.add('is-invalid');
      ket.closest('.form-control-wrap').nextElementSibling.textContent = 'Keterangan wajib diisi.';
      valid = false;
    }

    // Cek nominal
    const nom = document.getElementById('nominal');
    if (!nom.value || isNaN(nom.value) || parseInt(nom.value) <= 0) {
      nominalDisplay.classList.add('is-invalid');
      nominalDisplay.closest('.form-control-wrap').nextElementSibling.textContent = 'Nominal harus berupa angka dan lebih dari 0!';
      valid = false;
    }

    if (!valid) e.preventDefault();
  });

  // Hapus error saat user mengetik
  document.querySelectorAll('.form-control').forEach(el => {
    el.addEventListener('input', function() {
      this.classList.remove('is-invalid');
      let errContainer = this.nextElementSibling;
      if (!errContainer && this.closest('.form-control-wrap')) {
        errContainer = this.closest('.form-control-wrap').nextElementSibling;
      }
      if (errContainer && errContainer.classList.contains('form-error')) errContainer.textContent = '';
    });
    el.addEventListener('change', function() {
      this.classList.remove('is-invalid');
      let errContainer = this.nextElementSibling;
      if (!errContainer && this.closest('.form-control-wrap')) {
        errContainer = this.closest('.form-control-wrap').nextElementSibling;
      }
      if (errContainer && errContainer.classList.contains('form-error')) errContainer.textContent = '';
    });
  });
</script>
@endpush
