<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengajuan Dana BKM - {{ $periodeTitle }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 15mm 15mm 15mm 15mm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      color: #000;
      background: #fff;
      margin: 0;
      padding: 0;
    }
    .header {
      position: relative;
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 12px;
      min-height: 50px;
    }
    .header-logo {
      position: absolute;
      left: 0;
      top: 0;
      height: 48px;
    }
    .header h2 {
      font-size: 15px;
      font-weight: bold;
      margin: 0 0 4px 0;
      text-transform: uppercase;
    }
    .header h1 {
      font-size: 18px;
      font-weight: bold;
      margin: 0 0 4px 0;
      text-transform: uppercase;
    }
    .header p {
      font-size: 12px;
      font-weight: bold;
      margin: 0;
      color: #333;
    }
    
    .table-data {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .table-data th, .table-data td {
      border: 1px solid #000;
      padding: 5px 8px;
    }
    .table-data th {
      background-color: #f2f2f2;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 10px;
      text-align: center;
    }
    .group-header {
      background-color: #e6e6e6;
      font-weight: bold;
      font-size: 11px;
    }
    .group-subtotal {
      background-color: #f9f9f9;
      font-weight: bold;
    }
    .grand-total {
      background-color: #d9d9d9;
      font-weight: bold;
      font-size: 12px;
    }

    .section-title {
      font-size: 13px;
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 8px;
      text-transform: uppercase;
    }

    .table-penyaluran {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .table-penyaluran th, .table-penyaluran td {
      border: 1px solid #000;
      padding: 6px 8px;
    }
    .table-penyaluran th {
      background-color: #f2f2f2;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 10px;
      text-align: center;
    }

    .signature-container {
      width: 100%;
      page-break-inside: avoid;
      margin-top: 25px;
    }
    .date-location {
      text-align: right;
      font-size: 11px;
      margin-bottom: 15px;
    }
    .signature-table {
      width: 100%;
      border-collapse: collapse;
    }
    .signature-table td {
      width: 25%;
      text-align: center;
      vertical-align: top;
      padding: 0 5px;
    }
    .signature-title {
      font-weight: bold;
      font-size: 10px;
      margin-bottom: 90px; /* Space diperlebar untuk ttd fisik & stempel */
    }
    .signature-name {
      font-weight: bold;
      text-decoration: underline;
      font-size: 11px;
    }
    .signature-role {
      font-size: 10px;
      color: #444;
    }

    @media print {
      .no-print { display: none !important; }
      @page {
        size: A4 portrait;
        margin: 15mm 15mm 15mm 15mm;
      }
      body {
        margin: 0;
        padding: 0;
      }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="no-print" style="background: #1e293b; color: #fff; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; border-radius: 6px;">
    <div style="font-size: 12px; opacity: 0.9;">
      💡 <strong>Tips Cetak Bersih:</strong> Pada menu cetak browser, hapus centang <em>"Header dan footer"</em> agar URL/link tidak tertera di bagian bawah kertas.
    </div>
    <button onclick="window.print()" style="padding: 8px 18px; background: #7a0f1d; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px;">Cetak / Cetak Ke PDF</button>
  </div>

  <div class="header">
    <img src="{{ asset('images/logo_pt_bkm.png') }}" alt="Logo BKM" class="header-logo">
    <h2>PT. BERKAH KARYA MANSYUR</h2>
    <h1>PENGAJUAN DANA REIMBURSE BKM</h1>
    <p>{{ $periodeTitle }}</p>
  </div>

  <table class="table-data">
    <thead>
      <tr>
        <th style="width: 4%;">NO</th>
        <th style="width: 12%;">TANGGAL</th>
        <th style="width: 26%;">NAMA REKENING</th>
        <th style="width: 38%;">URAIAN</th>
        <th style="width: 20%;">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      @php $globalIndex = 1; @endphp
      @forelse($grouped as $dateKey => $rows)
        @php
          $subtotal = $rows->sum('total');
          $prevRek = null;
        @endphp
        @foreach($rows as $idx => $item)
          @php
            $currentRek = $item->rekeningBkm->nama_rekening ?? '-';
            $showTanggal = ($idx === 0) ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '';
            $showRek = ($currentRek !== $prevRek) ? $currentRek : '';
            $prevRek = $currentRek;
          @endphp
          <tr>
            <td style="text-align: center;">{{ $globalIndex++ }}</td>
            <td style="text-align: center;">{{ $showTanggal }}</td>
            <td>{{ $showRek }}</td>
            <td>{{ $item->uraian }}</td>
            <td style="text-align: right;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr class="group-subtotal">
          <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL TANGGAL {{ \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }}</td>
          <td style="text-align: right; font-weight: bold;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data pengajuan dana untuk periode ini.</td>
        </tr>
      @endforelse
      <tr class="grand-total">
        <td colspan="4" style="text-align: right; font-weight: bold; font-size: 11px;">GRAND TOTAL</td>
        <td style="text-align: right; font-weight: bold; font-size: 11px;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

  @if($penyaluran)
    <div class="section-title">PENYALURAN PEMBAYARAN</div>
    <table class="table-penyaluran">
      <thead>
        <tr>
          <th style="width: 5%;">NO</th>
          <th style="width: 35%;">KETERANGAN</th>
          <th style="width: 20%;">JUMLAH</th>
          <th style="width: 15%;">NO. REKENING</th>
          <th style="width: 10%;">BANK</th>
          <th style="width: 15%;">ATAS NAMA</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="text-align: center;">1</td>
          <td>{{ $penyaluran->deskripsi ?? 'REIMBURSE KAS BKM' }}</td>
          <td style="text-align: right; font-weight: bold;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
          <td style="text-align: center;">{{ $penyaluran->nomer_rekening }}</td>
          <td style="text-align: center;">{{ $penyaluran->bank }}</td>
          <td>{{ $penyaluran->nama }}</td>
        </tr>
      </tbody>
    </table>
  @endif

  <div class="signature-container">
    <div class="date-location">
      Surabaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>
    <table class="signature-table">
      <tr>
        <td>
          <div class="signature-title">Dibuat Oleh</div>
          <div class="signature-name">Rokha Rozalia</div>
          <div class="signature-role">Admin</div>
        </td>
        <td>
          <div class="signature-title">Diperiksa Oleh</div>
          <div class="signature-name">Budi Santosa</div>
          <div class="signature-role">Project Manager</div>
        </td>
        <td>
          <div class="signature-title">Disetujui Oleh</div>
          <div class="signature-name">Andreas</div>
          <div class="signature-role">Direktur Keuangan</div>
        </td>
        <td>
          <div class="signature-title">Disetujui Oleh</div>
          <div class="signature-name">Robin H Soegondo</div>
          <div class="signature-role">Direktur</div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
