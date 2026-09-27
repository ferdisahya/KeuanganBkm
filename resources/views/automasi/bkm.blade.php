@extends('layouts.app')

@section('title', 'Automasi BKM')
@section('topbar-title', 'Automasi BKM')
@section('topbar-subtitle', 'Ekstrak & impor puluhan ribu data transaksi Kas BKM dari file Excel')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
    <polyline points="14 2 14 8 20 8"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: #ffffff; border: 1px solid rgba(223, 187, 109, 0.3); box-shadow: 0 8px 24px rgba(122, 15, 29, 0.12); position: relative; }
  .dashboard-welcome-card::after { content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 100%; background: radial-gradient(circle, rgba(223, 187, 109, 0.15) 0%, transparent 70%); pointer-events: none; }
  
  .content-grid { display: flex; gap: 24px; flex-wrap: wrap; }
  .grid-col { flex: 1; min-width: 320px; }
  
  .form-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; padding: 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02); }
  
  .card-header-title { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; }
  .card-header-icon { width: 38px; height: 38px; border-radius: 10px; background: rgba(122, 15, 29, 0.08); color: #7a0f1d; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  
  .badge-stat { background: rgba(122, 15, 29, 0.06); border: 1px solid rgba(122, 15, 29, 0.12); border-radius: 10px; padding: 12px 18px; text-align: center; }
  .badge-stat-title { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
  .badge-stat-value { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #7a0f1d; margin-top: 2px; }

  .drop-zone { border: 2px dashed #cbd5e1; border-radius: 14px; padding: 32px 20px; text-align: center; background: #f8fafc; transition: all 0.25s ease; cursor: pointer; position: relative; }
  .drop-zone:hover, .drop-zone.dragover { border-color: #dfbb6d; background: #fffdf9; }
  .drop-zone input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
  
  .btn-primary-maroon { width: 100%; background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-weight: 700; padding: 14px 24px; border-radius: 12px; border: 1px solid rgba(223, 187, 109, 0.4); cursor: pointer; font-size: 14px; box-shadow: 0 4px 14px rgba(122, 15, 29, 0.2); transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; }
  .btn-primary-maroon:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122, 15, 29, 0.3); }

  .mapping-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-top: 16px; }
  .mapping-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; font-size: 12px; }
  .mapping-item strong { color: #1e293b; display: block; margin-bottom: 2px; }
  .mapping-item code { color: #7a0f1d; font-family: monospace; font-weight: 700; }
</style>

{{-- Banner Welcome --}}
<div class="dashboard-welcome-card">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <span style="color:#dfbb6d; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">⚡ Automasi Kas BKM (23.000+ Data)</span>
      <h1 style="font-family:'Outfit',sans-serif; font-size:26px; font-weight:800; margin:4px 0;">Automasi Transaksi BKM</h1>
      <p style="font-size:13.5px; opacity:0.85; font-weight:500; margin:0;">Unggah file Excel Laporan Kas BKM untuk memetakan puluhan ribu baris transaksi secara otomatis.</p>
    </div>
    
    <div class="badge-stat" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); min-width: 160px;">
      <div class="badge-stat-title" style="color: rgba(255,255,255,0.8);">Total Transaksi Kas BKM</div>
      <div class="badge-stat-value" style="color: #dfbb6d;">{{ number_format($totalKas) }}</div>
    </div>
  </div>
</div>

<div class="content-grid">
  
  {{-- Left Column: Reference Image & Column Mapping Rules --}}
  <div class="grid-col">
    <div class="form-card">
      <div class="card-header-title">
        <div class="card-header-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">Referensi Acuan Excel Transaksi BKM</h3>
          <span style="font-size:12px; color:#64748b;">Panduan layout & header kolom dari Kepala Keuangan</span>
        </div>
      </div>

      <div style="text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <img src="{{ asset('images/AutomasiBKM.png') }}" 
             alt="Referensi Automasi Transaksi BKM" 
             style="max-width: 100%; max-height: 240px; object-fit: contain; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
      </div>

      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 6px 0;">Pemetaan Kolom Excel BKM ke Database:</h4>
        
        <div class="mapping-grid">
          <div class="mapping-item">
            <strong>TANGGAL</strong>
            <code>kas_bkm.tanggal</code>
          </div>
          <div class="mapping-item">
            <strong>URAIAN</strong>
            <code>kas_bkm.uraian</code>
          </div>
          <div class="mapping-item">
            <strong>DEBET (Rp)</strong>
            <code>kas_bkm.debet</code>
          </div>
          <div class="mapping-item">
            <strong>KREDIT (Rp)</strong>
            <code>kas_bkm.kredit</code>
          </div>
          <div class="mapping-item">
            <strong>SALDO KAS</strong>
            <code>kas_bkm.saldo_kas</code>
          </div>
          <div class="mapping-item">
            <strong>KETERANGAN</strong>
            <code>kas_bkm.keterangan</code>
          </div>
          <div class="mapping-item">
            <strong>NO. REK & NAMA REK</strong>
            <code>rekening_bkm</code>
          </div>
          <div class="mapping-item">
            <strong>NO. SUB & NAMA SUB REK</strong>
            <code>subrekening_bkm</code>
          </div>
          <div class="mapping-item" style="grid-column: span 2;">
            <strong>NO. ITEM & NAMA ITEM</strong>
            <code>item_bkm</code>
          </div>
        </div>

        <div style="font-size: 11.5px; color: #64748b; margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 8px;">
          ℹ️ Nilai <code>#REF!</code> dan <code>-</code> otomatis dikosongkan. Kolom di luar acuan (Bulan, dll.) otomatis diabaikan.
        </div>
      </div>
    </div>
  </div>

  {{-- Right Column: Upload Area & Saldo Only Update --}}
  <div class="grid-col">

    {{-- Card 1: Hitung Ulang Saldo Database --}}
    <div class="form-card" style="border-top-color: #2563eb;">
      <div class="card-header-title">
        <div class="card-header-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">1-Click Hitung Ulang Saldo Kas BKM</h3>
          <span style="font-size:12px; color:#64748b;">Hitung ulang akumulasi saldo secara otomatis berdasarkan Debet & Kredit</span>
        </div>
      </div>

      <form action="{{ route('automasi.bkm.hitung_saldo') }}" method="POST" onsubmit="return confirm('Yakin ingin menghitung ulang saldo_kas untuk seluruh 23.000+ transaksi Kas BKM?')">
        @csrf
        <p style="font-size: 12.5px; color: #475569; margin-bottom: 16px;">
          Fitur ini akan secara otomatis memperbarui kolom <code>saldo_kas</code> pada data <code>kas_bkm</code> yang ada di database saat ini, tanpa mengubah data tanggal, uraian, debet, maupun kredit.
        </p>
        <button type="submit" class="btn-primary-maroon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-color: rgba(37, 99, 235, 0.4);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
          <span>🔄 Hitung Ulang Saldo Kas BKM Otomatis</span>
        </button>
      </form>
    </div>

    {{-- Card 2: Upload PDF / Excel Khusus Update Saldo --}}
    <div class="form-card" style="border-top-color: #16a34a;">
      <div class="card-header-title">
        <div class="card-header-icon" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">Upload PDF Khusus Update Saldo Kas BKM</h3>
            <span style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 999px; text-transform: uppercase;">Direkomendasikan</span>
          </div>
          <span style="font-size:12px; color:#64748b;">Mengekstrak kolom saldo bersih dari file PDF berdasarkan Tanggal & Uraian</span>
        </div>
      </div>

      <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; font-size: 12px; color: #166534; display: flex; gap: 10px; align-items: flex-start;">
        <span style="font-size: 16px;">💡</span>
        <div>
          <strong>Bebas Dari Gangguan Rumus Excel:</strong> Menggunakan file <code>.pdf</code> (seperti <em>Laporan_Kas_BKM.pdf</em>) memastikan nilai saldo terbaca murni angka asli tanpa formula yang rusak/corrupt. Sistem akan mencocokkan tiap baris berdasarkan <strong>Tanggal</strong> & <strong>Uraian</strong> secara presisi 100%.
        </div>
      </div>

      <form id="saldoUploadForm" action="{{ route('automasi.bkm.upload_saldo') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 12.5px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Pilih File PDF / Acuan Saldo BKM:</label>
          
          <div class="drop-zone" id="saldoDropZone" style="padding: 24px 16px;">
            <input type="file" name="excel_file" id="saldoFileInput" accept=".pdf,.xlsx,.xls,.csv" required onchange="handleSaldoFileSelect(this)">
            <div id="saldoDropZoneContent">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" style="margin: 0 auto 10px; display: block;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="m9 15 3-3 3 3"/></svg>
              <div style="font-size: 13.5px; font-weight: 700; color: #1e293b;">Klik atau Seret File .PDF / Excel ke Sini</div>
              <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Format didukung: <strong>.pdf</strong> (Utama), .xlsx, .xls, .csv (Maks. 100MB)</div>
            </div>
            
            <div id="saldoFileSelectedInfo" style="display: none; text-align: center;">
              <span style="font-size: 28px; display: block; margin-bottom: 4px;">📄</span>
              <div id="saldoFileName" style="font-size: 13px; font-weight: 700; color: #166534; word-break: break-all;"></div>
              <div id="saldoFileSize" style="font-size: 11.5px; color: #64748b; margin-top: 2px;"></div>
              <span style="display: inline-block; margin-top: 6px; font-size: 11px; color: #2563eb; text-decoration: underline;">Klik untuk ganti file</span>
            </div>
          </div>
        </div>

        <button type="submit" id="saldoSubmitBtn" class="btn-primary-maroon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); border-color: rgba(22, 163, 74, 0.4);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          <span>🚀 Update Saldo Kas BKM dari PDF / File</span>
        </button>

        {{-- Loading Spinner State --}}
        <div id="saldoLoadingProgress" style="display: none; margin-top: 14px; text-align: center; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
          <div style="display: inline-block; width: 26px; height: 26px; border: 3px solid #cbd5e1; border-top-color: #16a34a; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 8px;"></div>
          <div style="font-size: 13px; font-weight: 700; color: #1e293b;">Mengekstrak & Mencocokkan Saldo Kas BKM...</div>
          <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">Proses ekstraksi 700+ halaman PDF membutuhkan waktu sekitar 15-20 detik. Jangan tutup halaman ini.</div>
        </div>
      </form>
    </div>

  </div>

</div>

<style>
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

<script>
  function handleSaldoFileSelect(input) {
    if (input.files && input.files[0]) {
      const file = input.files[0];
      document.getElementById('saldoFileName').textContent = file.name;
      document.getElementById('saldoFileSize').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
      document.getElementById('saldoDropZoneContent').style.display = 'none';
      document.getElementById('saldoFileSelectedInfo').style.display = 'block';
    }
  }

  document.getElementById('saldoUploadForm').addEventListener('submit', function() {
    document.getElementById('saldoSubmitBtn').style.display = 'none';
    document.getElementById('saldoLoadingProgress').style.display = 'block';
  });
</script>
@endsection
