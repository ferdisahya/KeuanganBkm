<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Laporan Kas RAAB - PT BKM</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 12mm 15mm 12mm 15mm;
    }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
    .header { text-align: center; border-bottom: 2px solid #7a0f1d; padding-bottom: 12px; margin-bottom: 20px; }
    .header h2 { font-size: 20px; color: #7a0f1d; margin: 0 0 4px 0; text-transform: uppercase; }
    .header p { margin: 0; color: #64748b; font-size: 12px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 11px; }
    .table th { background: #f1f5f9; text-transform: uppercase; font-weight: 700; color: #334155; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    @media print {
      @page { size: A4 landscape; margin: 12mm 15mm 12mm 15mm; }
      body { margin: 0; padding: 0; }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="header">
    <h2>LAPORAN KAS RAAB</h2>
    <p>Dicetak Tanggal: {{ date('d F Y H:i') }} | Oleh: {{ auth()->user()->name ?? 'Admin Keuangan' }}</p>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th style="width: 30px;">No</th>
        <th>Tanggal</th>
        <th>Uraian</th>
        <th>No. Rekening RAAB</th>
        <th>Nama Rekening RAAB</th>
        <th>Subrekening RAAB</th>
        <th class="text-right">Debet</th>
        <th class="text-right">Kredit</th>
        <th class="text-right">Saldo</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transaksis as $index => $row)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
          <td><strong>{{ $row->uraian }}</strong></td>
          <td class="text-center">{{ $row->rekeningRaab->nomer_rekening ?? '-' }}</td>
          <td>{{ $row->rekeningRaab->nama_rekening ?? '-' }}</td>
          <td>{{ $row->subrekeningRaab->nama_subrekening ?? '-' }}</td>
          <td class="text-right">{{ $row->debet ? 'Rp ' . number_format($row->debet, 0, ',', '.') : '-' }}</td>
          <td class="text-right">{{ $row->kredit ? 'Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}</td>
          <td class="text-right">Rp {{ number_format($row->saldo, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="text-center">Tidak ada data transaksi RAAB.</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr style="background: #f8fafc; font-weight: bold;">
        <td colspan="6" class="text-right">TOTAL:</td>
        <td class="text-right" style="color: #2563eb;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
        <td class="text-right" style="color: #9333ea;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
        <td class="text-right" style="color: #3730a3;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
