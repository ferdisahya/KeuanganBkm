@extends('layouts.app')
@section('title', 'Pembelian Barang Logistik')
@section('topbar-title', 'Pembelian Barang')
@section('topbar-subtitle', 'Integrasi data pengadaan barang dari Sistem Logistik')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
    <line x1="3" y1="6" x2="21" y2="6"/>
    <path d="M16 10a4 4 0 0 1-8 0"/>
  </svg>
@endsection

@section('content')
<style>
  .dashboard-welcome-card { background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); border-radius: 16px; padding: 28px 36px; margin-bottom: 24px; color: #ffffff; border: 1px solid rgba(223, 187, 109, 0.3); box-shadow: 0 8px 24px rgba(122, 15, 29, 0.12); }
  .filter-card { background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #dfbb6d; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.01); }
  .form-control-custom { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 8px 14px; font-size: 13px; color: #1e293b; outline: none; transition: border-color 0.2s; }
  .form-control-custom:focus { border-color: #dfbb6d; background: #ffffff; }
  
  .table-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.015); overflow-x: auto; }
  .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
  .table-custom th { background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; border-bottom: 1.5px solid #e2e8f0; text-align: left; }
  .table-custom td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b; vertical-align: middle; }
  .table-custom tr:last-child td { border-bottom: none; }
  
  .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
  .badge-success { background: #e6f6ec; color: #16a34a; }
  .badge-warning { background: #fef3c7; color: #d97706; }
  .badge-danger { background: #fdf2f2; color: #dc2626; }
  .badge-info { background: #e0f2fe; color: #0284c7; }

  .btn-upload { background: #7a0f1d; color: #ffffff; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
  .btn-upload:hover { background: #5c0610; }
  .btn-view { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 5px 12px; border-radius: 8px; font-weight: 600; font-size: 11.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .btn-view:hover { background: #e2e8f0; color: #0f172a; }

  /* Modal Styling */
  .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999; }
  .modal-container { background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; padding: 28px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; }
  .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; }
  .modal-title { font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; color: #7a0f1d; margin: 0; }
  .btn-close-modal { background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; }
</style>

<div class="dashboard-welcome-card">
  <span style="color:#dfbb6d; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; display:inline-block; margin-bottom:4px;">Integrasi Logistik</span>
  <h1 style="font-family:'Outfit',sans-serif; font-size:26px; font-weight:800; margin:4px 0;">Pembelian Barang</h1>
  <p style="font-size:13.5px; opacity:0.85; margin:0;">Rekapitulasi transaksi pembelian barang dan pelunasan transfer oleh Keuangan.</p>
</div>

<div class="dashboard-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
  <div class="dashboard-stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 22px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 42px; height: 42px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 700;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Transaksi</div>
      <div style="font-size: 20px; font-weight: 800; color: #0f172a; font-family: 'Outfit', sans-serif;">{{ number_format($totalItemCount ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 22px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fdf2f8; color: #be185d; display: flex; align-items: center; justify-content: center; font-weight: 700;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Nominal (Terfilter)</div>
      <div style="font-size: 20px; font-weight: 800; color: #7a0f1d; font-family: 'Outfit', sans-serif;">Rp {{ number_format($totalNominal ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="dashboard-stat-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 22px; display: flex; align-items: center; gap: 16px;">
    <div style="width: 42px; height: 42px; border-radius: 10px; background: #e6f6ec; color: #16a34a; display: flex; align-items: center; justify-content: center; font-weight: 700;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Transaksi Selesai</div>
      <div style="font-size: 20px; font-weight: 800; color: #16a34a; font-family: 'Outfit', sans-serif;">{{ number_format($totalSelesai ?? 0, 0, ',', '.') }}</div>
    </div>
  </div>
</div>



<div class="filter-card">
  <form method="GET" action="{{ route('logistik.pembelian_barang') }}" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 240px;">
      <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control-custom" placeholder="Cari No. Pembelian / Barang / Divisi / Lokasi / Pembuat..." style="width: 100%;">
    </div>
    
    <div>
      <select name="status" class="form-control-custom" onchange="this.form.submit()">
        <option value="">-- Semua Status --</option>
        <option value="selesai" {{ ($status ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
        <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
        <option value="approved_pm" {{ ($status ?? '') === 'approved_pm' ? 'selected' : '' }}>Approved PM</option>
      </select>
    </div>

    <button type="submit" class="btn-primary-maroon" style="padding: 8px 18px; font-size: 12.5px; border-radius: 10px; background: #7a0f1d; color: white; border: none; font-weight: 600; cursor: pointer;">Filter</button>
  </form>
</div>

<div class="table-card">
  <table class="table-custom">
    <thead>
      <tr>
        <th>No. Pembelian</th>
        <th>Tanggal</th>
        <th>Nama Barang</th>
        <th>Divisi</th>
        <th>Lokasi</th>
        <th>Jumlah</th>
        <th>Total Harga</th>
        <th>Dibuat Oleh</th>
        <th>Status</th>
        <th>Bukti Transfer</th>
      </tr>
    </thead>
    <tbody>
      @forelse($pembelianList as $item)
        <tr>
          <td><strong style="color: #1e293b;">{{ $item->no_pembelian }}</strong></td>
          <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
          <td><span style="font-weight: 600; color: #0f172a;">{{ $item->nama_barang }}</span></td>
          <td>
            @if($item->nama_divisi && $item->nama_divisi !== '-')
              <span style="background: #f1f5f9; color: #334155; font-weight: 600; padding: 3px 9px; border-radius: 6px; font-size: 11.5px; border: 1px solid #e2e8f0; display: inline-block; white-space: nowrap;">
                {{ $item->nama_divisi }}
              </span>
            @else
              <span style="color: #94a3b8;">-</span>
            @endif
          </td>
          <td>
            @if($item->nama_lokasi && $item->nama_lokasi !== '-')
              <span style="color: #1e293b; font-weight: 600; font-size: 12.5px; white-space: nowrap;">
                {{ $item->nama_lokasi }}
              </span>
            @else
              <span style="color: #94a3b8;">-</span>
            @endif
          </td>
          <td>{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan }}</td>
          <td><strong style="color: #7a0f1d;">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</strong></td>
          <td>{{ $item->dibuat_oleh ?? '-' }}</td>
          <td>
            @if(in_array(strtolower($item->status), ['selesai', 'paid', 'approved']))
              <span class="badge-status badge-success">{{ $item->status }}</span>
            @elseif(in_array(strtolower($item->status), ['draft', 'pending']))
              <span class="badge-status badge-warning">{{ $item->status }}</span>
            @else
              <span class="badge-status badge-info">{{ $item->status }}</span>
            @endif
          </td>
          <td>
            @if(!empty($item->bukti_transfer))
              @php $transferPath = ltrim(preg_replace('#^(/?storage/|/?public/)#i', '', $item->bukti_transfer), '/'); @endphp
              <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                <span class="badge-status badge-success" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  Selesai
                </span>
                <a href="{{ url('storage/' . $transferPath) }}" target="_blank" class="btn-view">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  Lihat Bukti
                </a>
                @if($item->paid_at)
                  <span style="font-size: 10.5px; color: #64748b; font-weight: 500;">
                    Oleh: {{ $item->paid_by ?? 'Admin' }} ({{ \Carbon\Carbon::parse($item->paid_at)->format('d/m/Y H:i') }})
                  </span>
                @endif
              </div>
            @else
              @if(auth()->user()->role === 'Admin Keuangan')
                <button type="button" class="btn-upload" onclick="openUploadModal('{{ $item->id }}', '{{ $item->no_pembelian }}', 'Rp {{ number_format($item->total_harga, 0, ',', '.') }}')">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  Upload Bukti
                </button>
              @else
                <span style="font-size: 12px; color: #94a3b8; font-weight: 500;">Belum Transfer</span>
              @endif
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="10" style="text-align: center; padding: 32px; color: #94a3b8;">Tidak ada data Pembelian Barang.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top: 20px;">
    {{ $pembelianList->withQueryString()->links() }}
  </div>
</div>

<!-- Upload Modal (Pop up) -->
<div class="modal-overlay" id="uploadModal">
  <div class="modal-container">
    <div class="modal-header">
      <h3 class="modal-title">Upload Bukti Transfer</h3>
      <button type="button" class="btn-close-modal" onclick="closeUploadModal()">&times;</button>
    </div>

    <form id="uploadForm" method="POST" action="" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom: 16px;">
        <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">No. Pembelian</label>
        <div id="modalNoPembelian" style="font-weight: 700; font-size: 14px; color: #1e293b;">-</div>
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Total Nominal</label>
        <div id="modalTotalHarga" style="font-weight: 800; font-size: 16px; color: #7a0f1d; font-family: 'Outfit', sans-serif;">Rp 0</div>
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Potong dari Kas</label>
        <select name="tujuan_kas" required class="form-control-custom" style="width: 100%;">
          <option value="">-- Pilih Kas --</option>
          <option value="BKM">Kas BKM</option>
          <option value="RAAB">Kas RAAB</option>
        </select>
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Kode Rekening (Manual)</label>
        <input type="text" name="nomer_rekening" required class="form-control-custom" style="width: 100%;" placeholder="Contoh: 123.45">
      </div>

      <div style="margin-bottom: 16px;">
        <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 4px;">Subrekening (Manual)</label>
        <input type="text" name="nomer_subrekening" required class="form-control-custom" style="width: 100%;" placeholder="Contoh: 123.45.67">
      </div>

      <div style="margin-bottom: 20px;">
        <label style="font-size: 12px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;">Pilih File Bukti Transfer (JPG, PNG, PDF)</label>
        <input type="file" name="bukti_transfer" required accept="image/jpeg,image/png,application/pdf" class="form-control-custom" style="width: 100%;">
        <span style="font-size: 11px; color: #94a3b8; margin-top: 4px; display: block;">Maksimal ukuran file 10 MB.</span>
      </div>

      <div style="display: flex; gap: 10px; justify-content: flex-end;">
        <button type="button" onclick="closeUploadModal()" style="background: #f1f5f9; color: #64748b; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
        <button type="submit" style="background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(122,15,29,0.2);">Simpan & Transfer</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openUploadModal(id, noPembelian, totalHarga) {
    const modal = document.getElementById('uploadModal');
    const form = document.getElementById('uploadForm');
    const modalNo = document.getElementById('modalNoPembelian');
    const modalTotal = document.getElementById('modalTotalHarga');

    form.action = `/logistik/pembelian-barang/${id}/transfer`;
    modalNo.textContent = noPembelian;
    modalTotal.textContent = totalHarga;

    modal.style.display = 'flex';
  }

  function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
  }
</script>
@endsection
