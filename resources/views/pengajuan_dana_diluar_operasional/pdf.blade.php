<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengajuan Dana Diluar Operasional - {{ $periodeTitle }}</title>
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
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }
    .header h2 {
      font-size: 16px;
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
      padding: 6px 10px;
    }
    .table-data th {
      background-color: #f2f2f2;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 10px;
      text-align: center;
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

    .signature-container {
      width: 100%;
      page-break-inside: avoid;
      margin-top: 30px;
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
      margin-bottom: 90px;
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
    <h2>PT. BERKAH KARYA MANSYUR</h2>
    <h1>PENGAJUAN DANA DILUAR OPERASIONAL</h1>
    <p>{{ $periodeTitle }}</p>
  </div>

  <table class="table-data">
    <thead>
      <tr>
        <th style="width: 5%;">NO</th>
        <th style="width: 15%;">TANGGAL</th>
        <th style="width: 58%;">URAIAN</th>
        <th style="width: 22%;">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      @php $globalIndex = 1; @endphp
      @forelse($grouped as $dateKey => $rows)
        @php
          $subtotal = $rows->sum('total');
        @endphp
        @foreach($rows as $idx => $item)
          @php
            $showTanggal = ($idx === 0) ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '';
          @endphp
          <tr>
            <td style="text-align: center;">{{ $globalIndex++ }}</td>
            <td style="text-align: center;">{{ $showTanggal }}</td>
            <td>{{ $item->uraian }}</td>
            <td style="text-align: right;">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
          </tr>
        @endforeach
        <tr class="group-subtotal">
          <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL TANGGAL {{ \Carbon\Carbon::parse($dateKey)->format('d/m/Y') }}</td>
          <td style="text-align: right; font-weight: bold;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada data pengajuan dana diluar operasional untuk periode ini.</td>
        </tr>
      @endforelse
      <tr class="grand-total">
        <td colspan="3" style="text-align: right; font-weight: bold; font-size: 11px;">GRAND TOTAL</td>
        <td style="text-align: right; font-weight: bold; font-size: 11px;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
      </tr>
    </tbody>
  </table>

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
