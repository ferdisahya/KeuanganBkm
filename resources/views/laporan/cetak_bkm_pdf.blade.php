<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Laporan Kas BKM - PT BKM</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 12mm 15mm 12mm 15mm;
    }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
    .header { position: relative; text-align: center; border-bottom: 2px solid #7a0f1d; padding-bottom: 12px; margin-bottom: 20px; min-height: 50px; }
    .header-logo { position: absolute; left: 0; top: 0; height: 48px; }
    .header h2 { font-size: 20px; color: #7a0f1d; margin: 0 0 4px 0; text-transform: uppercase; }
    .header p { margin: 0; color: #64748b; font-size: 12px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 11px; }
    .table th { background: #f1f5f9; text-transform: uppercase; font-weight: 700; color: #334155; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .summary-box { margin-top: 20px; float: right; width: 300px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12px; }
    .summary-row.total { font-weight: 800; border-top: 1px solid #e2e8f0; padding-top: 6px; color: #7a0f1d; }
    @media print {
      @page { size: A4 landscape; margin: 12mm 15mm 12mm 15mm; }
      body { margin: 0; padding: 0; }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="header">
    <img src="{{ asset('images/logo_pt_bkm.png') }}" alt="Logo BKM" class="header-logo">
    <h2>LAPORAN KAS BKM PT BKM</h2>
    <p>Dicetak Tanggal: {{ date('d F Y H:i') }} | Oleh: {{ auth()->user()->name ?? 'Admin Keuangan' }}</p>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th style="width: 30px;">No</th>
        <th>Tanggal</th>
        <th>Uraian</th>
        <th>No. Rekening</th>
        <th>Nama Rekening</th>
        <th>Subrekening BKM</th>
        <th>Item BKM</th>
        <th class="text-right">Debet</th>
        <th class="text-right">Kredit</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transaksis as $index => $row)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
          <td><strong>{{ $row->uraian }}</strong></td>
          <td class="text-center">{{ $row->rekeningBkm->nomer_rekening ?? '-' }}</td>
          <td>{{ $row->rekeningBkm->nama_rekening ?? '-' }}</td>
          <td>{{ $row->subrekeningBkm->nama_subrekening ?? '-' }}</td>
          <td>{{ $row->itemBkm->nama_item ?? '-' }}</td>
          <td class="text-right">{{ $row->debet ? 'Rp ' . number_format($row->debet, 0, ',', '.') : '-' }}</td>
          <td class="text-right">{{ $row->kredit ? 'Rp ' . number_format($row->kredit, 0, ',', '.') : '-' }}</td>
          <td>{{ $row->keterangan ?: '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center">Tidak ada data transaksi.</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr style="background: #f8fafc; font-weight: bold;">
        <td colspan="7" class="text-right">TOTAL:</td>
        <td class="text-right" style="color: #16a34a;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
        <td class="text-right" style="color: #dc2626;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
        <td></td>
      </tr>
      <tr style="background: #f1f5f9; font-weight: 800;">
        <td colspan="7" class="text-right">SALDO AKHIR BKM:</td>
        <td colspan="2" class="text-right" style="color: #7a0f1d; font-size: 13px;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
