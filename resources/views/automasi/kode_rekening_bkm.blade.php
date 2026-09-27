@extends('layouts.app')

@section('title', 'Automasi Kode Rekening BKM')
@section('topbar-title', 'Automasi Kode Rekening BKM')
@section('topbar-subtitle', 'Ekstrak & impor otomatis master rekening & subrekening BKM dari Excel')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/>
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

  .rule-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin-top: 20px; }
  .rule-box ul { margin: 0; padding-left: 20px; }
  .rule-box li { font-size: 13px; color: #475569; margin-bottom: 8px; line-height: 1.5; }
  .rule-box li:last-child { margin-bottom: 0; }
</style>

{{-- Banner Welcome --}}
<div class="dashboard-welcome-card">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <span style="color:#dfbb6d; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">⚡ Automasi Master Data BKM</span>
      <h1 style="font-family:'Outfit',sans-serif; font-size:26px; font-weight:800; margin:4px 0;">Automasi Kode Rekening BKM</h1>
      <p style="font-size:13.5px; opacity:0.85; font-weight:500; margin:0;">Upload file Excel untuk mengekstrak dan menyinkronkan master rekening & subrekening BKM secara otomatis.</p>
    </div>
    
    <div style="display: flex; gap: 12px;">
      <div class="badge-stat" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
        <div class="badge-stat-title" style="color: rgba(255,255,255,0.8);">Total Rekening BKM</div>
        <div class="badge-stat-value" style="color: #dfbb6d;">{{ number_format($totalRekening) }}</div>
      </div>
      <div class="badge-stat" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
        <div class="badge-stat-title" style="color: rgba(255,255,255,0.8);">Total Subrekening BKM</div>
        <div class="badge-stat-value" style="color: #ffffff;">{{ number_format($totalSubrekening) }}</div>
      </div>
    </div>
  </div>
</div>

<div class="content-grid">
  
  {{-- Left Column: Reference Image & Extraction Rules --}}
  <div class="grid-col">
    <div class="form-card">
      <div class="card-header-title">
        <div class="card-header-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">Referensi Acuan Excel BKM</h3>
          <span style="font-size:12px; color:#64748b;">Panduan format kolom Excel Kepala Keuangan</span>
        </div>
      </div>

      <div style="text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
        <img src="{{ asset('images/KodeRekeningBKM.png') }}" 
             alt="Referensi Kode Rekening BKM" 
             style="max-width: 100%; max-height: 240px; object-fit: contain; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
      </div>

      <div class="rule-box">
        <h4 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 10px 0;">Aturan Ekstraksi Otomatis Database BKM:</h4>
        <ul>
          <li><strong>Kolom A:</strong> Berisi <code>Nomer Rekening / Subrekening</code> (misal: 222.00, 222.01).</li>
          <li><strong>Kolom B:</strong> Berisi <code>Nama Rekening / Subrekening</code> (misal: Kas Tambak, Kas Operasional).</li>
          <li><strong style="color: #7a0f1d;">Tulisan BOLD (Cetak Tebal):</strong> Masuk ke tabel database <code style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px;">rekening_bkm</code>.</li>
          <li><strong style="color: #475569;">Tulisan NORMAL (Tidak Tebal):</strong> Masuk ke tabel database <code style="background:#fef3c7; color:#92400e; padding:2px 6px; border-radius:4px;">subrekening_bkm</code>.</li>
        </ul>
      </div>
    </div>
  </div>

  {{-- Right Column: Upload Area --}}
  <div class="grid-col">
    <div class="form-card">
      <div class="card-header-title">
        <div class="card-header-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; color:#1e293b; margin:0;">Upload File Excel</h3>
          <span style="font-size:12px; color:#64748b;">Pilih atau drag & drop file Excel (.xlsx, .xls, .csv)</span>
        </div>
      </div>

      <form action="{{ route('automasi.kode_rekening_bkm.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <div style="margin-bottom: 24px;">
          <div class="drop-zone" id="dropZone">
            <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls,.csv" required onchange="handleFileSelect(this)">
            
            <div id="dropZoneContent">
              <div style="color: #7a0f1d; margin-bottom: 12px;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/></svg>
              </div>
              <h4 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">Klik untuk Pilih File atau Drag & Drop</h4>
              <p style="font-size: 12px; color: #94a3b8; margin: 0;">Format didukung: .XLSX, .XLS, .CSV (Maksimal: 50 MB)</p>
            </div>

            <div id="fileSelectedInfo" style="display: none;">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; display: inline-flex; align-items: center; gap: 12px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                <div style="text-align: left;">
                  <div id="fileName" style="font-size: 13px; font-weight: 700; color: #1e293b;">file.xlsx</div>
                  <div id="fileSize" style="font-size: 11px; color: #94a3b8;">0 KB</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-primary-maroon" id="submitBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          <span>Proses & Impor Kode Rekening BKM</span>
        </button>

        <div id="loadingProgress" style="display: none; text-align: center; margin-top: 20px;">
          <div style="font-size: 14px; font-weight: 700; color: #7a0f1d; margin-bottom: 4px;">⏳ Memproses & Mengekstrak Data Excel BKM...</div>
          <span style="font-size: 12px; color: #64748b;">Mohon tunggu beberapa saat, sistem sedang menyinkronkan data ke database.</span>
        </div>
      </form>

    </div>
  </div>

</div>

<script>
  function handleFileSelect(input) {
    if (input.files && input.files[0]) {
      const file = input.files[0];
      document.getElementById('fileName').textContent = file.name;
      document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
      document.getElementById('dropZoneContent').style.display = 'none';
      document.getElementById('fileSelectedInfo').style.display = 'block';
    }
  }

  document.getElementById('uploadForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').style.display = 'none';
    document.getElementById('loadingProgress').style.display = 'block';
  });
</script>
@endsection
