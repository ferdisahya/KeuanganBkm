<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasBkmController;
use App\Http\Controllers\KasRaabController;
use App\Http\Controllers\SisaAnggaranBkmController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\LogistikController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AutomasiController;
use App\Http\Controllers\PengajuanDanaBkmController;
use App\Http\Controllers\PengajuanDanaRaabController;
use App\Http\Controllers\FormAjukanDanaBkmController;
use App\Http\Controllers\FormAjukanDanaRaabController;
use App\Http\Controllers\PengajuanDanaDiluarOperasionalController;
use App\Http\Controllers\FormAjukanDanaDiluarOperasionalController;
use App\Http\Controllers\PengajuanDanaGajiController;
use App\Http\Controllers\FormAjukanDanaGajiController;
use App\Http\Controllers\PengajuanDanaSkmController;
use App\Http\Controllers\FormAjukanDanaSkmController;
use App\Http\Controllers\DeviceLockoutController;
use App\Http\Controllers\StorageFileController;
use App\Http\Middleware\CheckBlockedIp;
use App\Http\Middleware\CheckSessionTimeout;

// Guest Routes
Route::middleware(['guest', CheckBlockedIp::class])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
});

// Google Auth
Route::middleware([CheckBlockedIp::class])->group(function () {
    Route::get('/auth/google',           [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback',  [GoogleController::class, 'handleGoogleCallback']);
    Route::get('/auth/bypass',           [GoogleController::class, 'bypassLogin'])->name('auth.bypass');
});

Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Protected App Routes
Route::middleware(['auth', CheckSessionTimeout::class])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kas BKM
    Route::get('/kas-bkm',                 [KasBkmController::class, 'index'])->name('kas_bkm.index');
    Route::get('/kas-bkm/create',          [KasBkmController::class, 'create'])->name('kas_bkm.create');
    Route::post('/kas-bkm',                [KasBkmController::class, 'store'])->name('kas_bkm.store');
    Route::get('/kas-bkm/{kasBkm}/edit',   [KasBkmController::class, 'edit'])->name('kas_bkm.edit');
    Route::put('/kas-bkm/{kasBkm}',        [KasBkmController::class, 'update'])->name('kas_bkm.update');
    Route::delete('/kas-bkm/{kasBkm}',     [KasBkmController::class, 'destroy'])->name('kas_bkm.destroy');

    // Legacy route alias for compatibility
    Route::get('/transaksi', function() { return redirect()->route('kas_bkm.index'); })->name('transaksi.index');
    Route::get('/transaksi/create', function() { return redirect()->route('kas_bkm.create'); })->name('transaksi.create');

    // Kas RAAB
    Route::get('/kas-raab',                 [KasRaabController::class, 'index'])->name('kas_raab.index');
    Route::get('/kas-raab/create',          [KasRaabController::class, 'create'])->name('kas_raab.create');
    Route::post('/kas-raab',                [KasRaabController::class, 'store'])->name('kas_raab.store');
    Route::get('/kas-raab/{kasRaab}/edit',  [KasRaabController::class, 'edit'])->name('kas_raab.edit');
    Route::put('/kas-raab/{kasRaab}',       [KasRaabController::class, 'update'])->name('kas_raab.update');
    Route::delete('/kas-raab/{kasRaab}',    [KasRaabController::class, 'destroy'])->name('kas_raab.destroy');

    // Sisa Anggaran BKM
    Route::get('/sisa-anggaran',                        [SisaAnggaranBkmController::class, 'index'])->name('sisa_anggaran.index');
    Route::get('/sisa-anggaran/create',                 [SisaAnggaranBkmController::class, 'create'])->name('sisa_anggaran.create');
    Route::post('/sisa-anggaran',                       [SisaAnggaranBkmController::class, 'store'])->name('sisa_anggaran.store');
    Route::get('/sisa-anggaran/{sisaAnggaran}/edit',    [SisaAnggaranBkmController::class, 'edit'])->name('sisa_anggaran.edit');
    Route::put('/sisa-anggaran/{sisaAnggaran}',         [SisaAnggaranBkmController::class, 'update'])->name('sisa_anggaran.update');
    Route::delete('/sisa-anggaran/{sisaAnggaran}',      [SisaAnggaranBkmController::class, 'destroy'])->name('sisa_anggaran.destroy');

    // Pengajuan Dana BKM
    Route::get('/pengajuan-dana-bkm',                   [PengajuanDanaBkmController::class, 'index'])->name('pengajuan_dana_bkm.index');
    Route::get('/pengajuan-dana-bkm/pdf',               [PengajuanDanaBkmController::class, 'cetakPdf'])->name('pengajuan_dana_bkm.pdf');
    Route::get('/pengajuan-dana-bkm/create',            [PengajuanDanaBkmController::class, 'create'])->name('pengajuan_dana_bkm.create');
    Route::post('/pengajuan-dana-bkm',                  [PengajuanDanaBkmController::class, 'store'])->name('pengajuan_dana_bkm.store');
    Route::get('/pengajuan-dana-bkm/{id}/edit',         [PengajuanDanaBkmController::class, 'edit'])->name('pengajuan_dana_bkm.edit');
    Route::put('/pengajuan-dana-bkm/{id}',              [PengajuanDanaBkmController::class, 'update'])->name('pengajuan_dana_bkm.update');
    Route::delete('/pengajuan-dana-bkm/{id}',           [PengajuanDanaBkmController::class, 'destroy'])->name('pengajuan_dana_bkm.destroy');

    // Form Ajukan Dana BKM
    Route::get('/form-ajukan-dana-bkm',                 [FormAjukanDanaBkmController::class, 'index'])->name('form_ajukan_dana_bkm.index');
    Route::post('/form-ajukan-dana-bkm',                [FormAjukanDanaBkmController::class, 'store'])->name('form_ajukan_dana_bkm.store');
    Route::post('/form-ajukan-dana-bkm/{id}/approve',   [FormAjukanDanaBkmController::class, 'approve'])->name('form_ajukan_dana_bkm.approve');
    Route::post('/form-ajukan-dana-bkm/{id}/reject',    [FormAjukanDanaBkmController::class, 'reject'])->name('form_ajukan_dana_bkm.reject');
    Route::post('/form-ajukan-dana-bkm/{id}/upload-bukti',[FormAjukanDanaBkmController::class, 'uploadBukti'])->name('form_ajukan_dana_bkm.upload_bukti');
    Route::delete('/form-ajukan-dana-bkm/{id}',         [FormAjukanDanaBkmController::class, 'destroy'])->name('form_ajukan_dana_bkm.destroy');

    // Pengajuan Dana RAAB
    Route::get('/pengajuan-dana-raab',                  [PengajuanDanaRaabController::class, 'index'])->name('pengajuan_dana_raab.index');
    Route::get('/pengajuan-dana-raab/pdf',              [PengajuanDanaRaabController::class, 'cetakPdf'])->name('pengajuan_dana_raab.pdf');
    Route::get('/pengajuan-dana-raab/create',           [PengajuanDanaRaabController::class, 'create'])->name('pengajuan_dana_raab.create');
    Route::post('/pengajuan-dana-raab',                 [PengajuanDanaRaabController::class, 'store'])->name('pengajuan_dana_raab.store');
    Route::get('/pengajuan-dana-raab/{id}/edit',        [PengajuanDanaRaabController::class, 'edit'])->name('pengajuan_dana_raab.edit');
    Route::put('/pengajuan-dana-raab/{id}',             [PengajuanDanaRaabController::class, 'update'])->name('pengajuan_dana_raab.update');
    Route::delete('/pengajuan-dana-raab/{id}',          [PengajuanDanaRaabController::class, 'destroy'])->name('pengajuan_dana_raab.destroy');

    // Form Ajukan Dana RAAB
    Route::get('/form-ajukan-dana-raab',                [FormAjukanDanaRaabController::class, 'index'])->name('form_ajukan_dana_raab.index');
    Route::post('/form-ajukan-dana-raab',               [FormAjukanDanaRaabController::class, 'store'])->name('form_ajukan_dana_raab.store');
    Route::post('/form-ajukan-dana-raab/{id}/approve',  [FormAjukanDanaRaabController::class, 'approve'])->name('form_ajukan_dana_raab.approve');
    Route::post('/form-ajukan-dana-raab/{id}/reject',   [FormAjukanDanaRaabController::class, 'reject'])->name('form_ajukan_dana_raab.reject');
    Route::post('/form-ajukan-dana-raab/{id}/upload-bukti',[FormAjukanDanaRaabController::class, 'uploadBukti'])->name('form_ajukan_dana_raab.upload_bukti');
    Route::delete('/form-ajukan-dana-raab/{id}',        [FormAjukanDanaRaabController::class, 'destroy'])->name('form_ajukan_dana_raab.destroy');

    // Pengajuan Dana Diluar Operasional
    Route::get('/pengajuan-dana-diluar-operasional',                  [PengajuanDanaDiluarOperasionalController::class, 'index'])->name('pengajuan_dana_diluar_operasional.index');
    Route::get('/pengajuan-dana-diluar-operasional/pdf',              [PengajuanDanaDiluarOperasionalController::class, 'cetakPdf'])->name('pengajuan_dana_diluar_operasional.pdf');
    Route::get('/pengajuan-dana-diluar-operasional/create',           [PengajuanDanaDiluarOperasionalController::class, 'create'])->name('pengajuan_dana_diluar_operasional.create');
    Route::post('/pengajuan-dana-diluar-operasional',                 [PengajuanDanaDiluarOperasionalController::class, 'store'])->name('pengajuan_dana_diluar_operasional.store');
    Route::get('/pengajuan-dana-diluar-operasional/{id}/edit',        [PengajuanDanaDiluarOperasionalController::class, 'edit'])->name('pengajuan_dana_diluar_operasional.edit');
    Route::put('/pengajuan-dana-diluar-operasional/{id}',             [PengajuanDanaDiluarOperasionalController::class, 'update'])->name('pengajuan_dana_diluar_operasional.update');
    Route::delete('/pengajuan-dana-diluar-operasional/{id}',          [PengajuanDanaDiluarOperasionalController::class, 'destroy'])->name('pengajuan_dana_diluar_operasional.destroy');

    // Form Ajukan Dana Diluar Operasional
    Route::get('/form-ajukan-dana-diluar-operasional',                [FormAjukanDanaDiluarOperasionalController::class, 'index'])->name('form_ajukan_dana_diluar_operasional.index');
    Route::post('/form-ajukan-dana-diluar-operasional',               [FormAjukanDanaDiluarOperasionalController::class, 'store'])->name('form_ajukan_dana_diluar_operasional.store');
    Route::post('/form-ajukan-dana-diluar-operasional/{id}/approve',  [FormAjukanDanaDiluarOperasionalController::class, 'approve'])->name('form_ajukan_dana_diluar_operasional.approve');
    Route::post('/form-ajukan-dana-diluar-operasional/{id}/reject',   [FormAjukanDanaDiluarOperasionalController::class, 'reject'])->name('form_ajukan_dana_diluar_operasional.reject');
    Route::post('/form-ajukan-dana-diluar-operasional/{id}/upload-bukti',[FormAjukanDanaDiluarOperasionalController::class, 'uploadBukti'])->name('form_ajukan_dana_diluar_operasional.upload_bukti');
    Route::delete('/form-ajukan-dana-diluar-operasional/{id}',        [FormAjukanDanaDiluarOperasionalController::class, 'destroy'])->name('form_ajukan_dana_diluar_operasional.destroy');

    // Pengajuan Dana Gaji
    Route::get('/pengajuan-dana-gaji',                  [PengajuanDanaGajiController::class, 'index'])->name('pengajuan_dana_gaji.index');
    Route::get('/pengajuan-dana-gaji/pdf',              [PengajuanDanaGajiController::class, 'cetakPdf'])->name('pengajuan_dana_gaji.pdf');
    Route::get('/pengajuan-dana-gaji/create',           [PengajuanDanaGajiController::class, 'create'])->name('pengajuan_dana_gaji.create');
    Route::post('/pengajuan-dana-gaji',                 [PengajuanDanaGajiController::class, 'store'])->name('pengajuan_dana_gaji.store');
    Route::get('/pengajuan-dana-gaji/{id}/edit',        [PengajuanDanaGajiController::class, 'edit'])->name('pengajuan_dana_gaji.edit');
    Route::put('/pengajuan-dana-gaji/{id}',             [PengajuanDanaGajiController::class, 'update'])->name('pengajuan_dana_gaji.update');
    Route::delete('/pengajuan-dana-gaji/{id}',          [PengajuanDanaGajiController::class, 'destroy'])->name('pengajuan_dana_gaji.destroy');

    // Form Ajukan Dana Gaji
    Route::get('/form-ajukan-dana-gaji',                [FormAjukanDanaGajiController::class, 'index'])->name('form_ajukan_dana_gaji.index');
    Route::post('/form-ajukan-dana-gaji',               [FormAjukanDanaGajiController::class, 'store'])->name('form_ajukan_dana_gaji.store');
    Route::post('/form-ajukan-dana-gaji/{id}/approve',  [FormAjukanDanaGajiController::class, 'approve'])->name('form_ajukan_dana_gaji.approve');
    Route::post('/form-ajukan-dana-gaji/{id}/reject',   [FormAjukanDanaGajiController::class, 'reject'])->name('form_ajukan_dana_gaji.reject');
    Route::post('/form-ajukan-dana-gaji/{id}/upload-bukti',[FormAjukanDanaGajiController::class, 'uploadBukti'])->name('form_ajukan_dana_gaji.upload_bukti');
    Route::delete('/form-ajukan-dana-gaji/{id}',        [FormAjukanDanaGajiController::class, 'destroy'])->name('form_ajukan_dana_gaji.destroy');

    // Pengajuan Dana SKM
    Route::get('/pengajuan-dana-skm',                  [PengajuanDanaSkmController::class, 'index'])->name('pengajuan_dana_skm.index');
    Route::get('/pengajuan-dana-skm/pdf',              [PengajuanDanaSkmController::class, 'cetakPdf'])->name('pengajuan_dana_skm.pdf');
    Route::get('/pengajuan-dana-skm/create',           [PengajuanDanaSkmController::class, 'create'])->name('pengajuan_dana_skm.create');
    Route::post('/pengajuan-dana-skm',                 [PengajuanDanaSkmController::class, 'store'])->name('pengajuan_dana_skm.store');
    Route::get('/pengajuan-dana-skm/{id}/edit',        [PengajuanDanaSkmController::class, 'edit'])->name('pengajuan_dana_skm.edit');
    Route::put('/pengajuan-dana-skm/{id}',             [PengajuanDanaSkmController::class, 'update'])->name('pengajuan_dana_skm.update');
    Route::delete('/pengajuan-dana-skm/{id}',          [PengajuanDanaSkmController::class, 'destroy'])->name('pengajuan_dana_skm.destroy');

    // Form Ajukan Dana SKM
    Route::get('/form-ajukan-dana-skm',                [FormAjukanDanaSkmController::class, 'index'])->name('form_ajukan_dana_skm.index');
    Route::post('/form-ajukan-dana-skm',               [FormAjukanDanaSkmController::class, 'store'])->name('form_ajukan_dana_skm.store');
    Route::post('/form-ajukan-dana-skm/{id}/approve',  [FormAjukanDanaSkmController::class, 'approve'])->name('form_ajukan_dana_skm.approve');
    Route::post('/form-ajukan-dana-skm/{id}/reject',   [FormAjukanDanaSkmController::class, 'reject'])->name('form_ajukan_dana_skm.reject');
    Route::post('/form-ajukan-dana-skm/{id}/upload-bukti',[FormAjukanDanaSkmController::class, 'uploadBukti'])->name('form_ajukan_dana_skm.upload_bukti');
    Route::delete('/form-ajukan-dana-skm/{id}',        [FormAjukanDanaSkmController::class, 'destroy'])->name('form_ajukan_dana_skm.destroy');

    // Subbab Master Data (Dedicated Sub-pages)
    Route::get('/master-data',                                [MasterDataController::class, 'rekeningBkmIndex'])->name('master.index');
    Route::get('/master-data/rekening-bkm',                   [MasterDataController::class, 'rekeningBkmIndex'])->name('master.rekening_bkm.index');
    Route::post('/master-data/rekening-bkm',                  [MasterDataController::class, 'storeRekeningBkm'])->name('master.rekening_bkm.store');
    Route::put('/master-data/rekening-bkm/{rekeningBkm}',     [MasterDataController::class, 'updateRekeningBkm'])->name('master.rekening_bkm.update');
    Route::delete('/master-data/rekening-bkm/{rekeningBkm}',   [MasterDataController::class, 'destroyRekeningBkm'])->name('master.rekening_bkm.destroy');

    Route::get('/master-data/rekening-raab',                  [MasterDataController::class, 'rekeningRaabIndex'])->name('master.rekening_raab.index');
    Route::post('/master-data/rekening-raab',                 [MasterDataController::class, 'storeRekeningRaab'])->name('master.rekening_raab.store');
    Route::put('/master-data/rekening-raab/{rekeningRaab}',    [MasterDataController::class, 'updateRekeningRaab'])->name('master.rekening_raab.update');
    Route::delete('/master-data/rekening-raab/{rekeningRaab}',[MasterDataController::class, 'destroyRekeningRaab'])->name('master.rekening_raab.destroy');

    Route::get('/master-data/subrekening-bkm',                [MasterDataController::class, 'subrekeningBkmIndex'])->name('master.subrekening_bkm.index');
    Route::post('/master-data/subrekening-bkm',               [MasterDataController::class, 'storeSubrekeningBkm'])->name('master.subrekening_bkm.store');
    Route::put('/master-data/subrekening-bkm/{subrekeningBkm}',[MasterDataController::class, 'updateSubrekeningBkm'])->name('master.subrekening_bkm.update');
    Route::delete('/master-data/subrekening-bkm/{subrekeningBkm}', [MasterDataController::class, 'destroySubrekeningBkm'])->name('master.subrekening_bkm.destroy');

    Route::get('/master-data/subrekening-raab',               [MasterDataController::class, 'subrekeningRaabIndex'])->name('master.subrekening_raab.index');
    Route::post('/master-data/subrekening-raab',              [MasterDataController::class, 'storeSubrekeningRaab'])->name('master.subrekening_raab.store');
    Route::put('/master-data/subrekening-raab/{subrekeningRaab}', [MasterDataController::class, 'updateSubrekeningRaab'])->name('master.subrekening_raab.update');
    Route::delete('/master-data/subrekening-raab/{subrekeningRaab}', [MasterDataController::class, 'destroySubrekeningRaab'])->name('master.subrekening_raab.destroy');

    Route::get('/master-data/item-bkm',                       [MasterDataController::class, 'itemBkmIndex'])->name('master.item_bkm.index');
    Route::post('/master-data/item-bkm',                      [MasterDataController::class, 'storeItemBkm'])->name('master.item_bkm.store');
    Route::put('/master-data/item-bkm/{itemBkm}',             [MasterDataController::class, 'updateItemBkm'])->name('master.item_bkm.update');
    Route::delete('/master-data/item-bkm/{itemBkm}',          [MasterDataController::class, 'destroyItemBkm'])->name('master.item_bkm.destroy');

    Route::get('/master-data/penyaluran-pembayaran-bkm',                     [MasterDataController::class, 'penyaluranPembayaranBkmIndex'])->name('master.penyaluran_pembayaran_bkm.index');
    Route::post('/master-data/penyaluran-pembayaran-bkm',                    [MasterDataController::class, 'storePenyaluranPembayaranBkm'])->name('master.penyaluran_pembayaran_bkm.store');
    Route::put('/master-data/penyaluran-pembayaran-bkm/{penyaluranBkm}',     [MasterDataController::class, 'updatePenyaluranPembayaranBkm'])->name('master.penyaluran_pembayaran_bkm.update');
    Route::delete('/master-data/penyaluran-pembayaran-bkm/{penyaluranBkm}',  [MasterDataController::class, 'destroyPenyaluranPembayaranBkm'])->name('master.penyaluran_pembayaran_bkm.destroy');

    Route::get('/master-data/penyaluran-pembayaran-raab',                    [MasterDataController::class, 'penyaluranPembayaranRaabIndex'])->name('master.penyaluran_pembayaran_raab.index');
    Route::post('/master-data/penyaluran-pembayaran-raab',                   [MasterDataController::class, 'storePenyaluranPembayaranRaab'])->name('master.penyaluran_pembayaran_raab.store');
    Route::put('/master-data/penyaluran-pembayaran-raab/{penyaluranRaab}',    [MasterDataController::class, 'updatePenyaluranPembayaranRaab'])->name('master.penyaluran_pembayaran_raab.update');
    Route::delete('/master-data/penyaluran-pembayaran-raab/{penyaluranRaab}', [MasterDataController::class, 'destroyPenyaluranPembayaranRaab'])->name('master.penyaluran_pembayaran_raab.destroy');

    // Integration with Logistics System (Hosted/Released)
    Route::get('/logistik/pembelian-barang',                  [LogistikController::class, 'pembelianBarang'])->name('logistik.pembelian_barang');
    Route::post('/logistik/pembelian-barang/{id}/transfer',    [LogistikController::class, 'uploadBuktiTransferPembelian'])->name('logistik.pembelian_barang.transfer');
    Route::get('/logistik/kebutuhan-langsung',               [LogistikController::class, 'kebutuhanLangsung'])->name('logistik.kebutuhan_langsung');
    Route::post('/logistik/kebutuhan-langsung/{id}/transfer', [LogistikController::class, 'uploadBuktiTransferKebutuhanLangsung'])->name('logistik.kebutuhan_langsung.transfer');

    // Subbab Laporan (Laporan BKM & Laporan RAAB)
    Route::get('/laporan/bkm',      [LaporanController::class, 'laporanBkm'])->name('laporan.bkm');
    Route::get('/laporan/raab',     [LaporanController::class, 'laporanRaab'])->name('laporan.raab');
    Route::get('/laporan/harian',   [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan',  [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('/laporan/tahunan',  [LaporanController::class, 'tahunan'])->name('laporan.tahunan');

    // Subbab Automasi Data
    Route::get('/automasi/kode-rekening-raab',         [AutomasiController::class, 'kodeRekeningRaabIndex'])->name('automasi.kode_rekening_raab.index');
    Route::post('/automasi/kode-rekening-raab/upload', [AutomasiController::class, 'uploadKodeRekeningRaab'])->name('automasi.kode_rekening_raab.upload');

    Route::get('/automasi/raab',                       [AutomasiController::class, 'raabIndex'])->name('automasi.raab.index');
    Route::post('/automasi/raab/upload',                [AutomasiController::class, 'uploadRaab'])->name('automasi.raab.upload');
    Route::post('/automasi/raab/upload-saldo',          [AutomasiController::class, 'uploadSaldoRaab'])->name('automasi.raab.upload_saldo');
    Route::post('/automasi/raab/hitung-saldo',          [AutomasiController::class, 'hitungUlangSaldoRaab'])->name('automasi.raab.hitung_saldo');

    Route::get('/automasi/kode-rekening-bkm',          [AutomasiController::class, 'kodeRekeningBkmIndex'])->name('automasi.kode_rekening_bkm.index');
    Route::post('/automasi/kode-rekening-bkm/upload',   [AutomasiController::class, 'uploadKodeRekeningBkm'])->name('automasi.kode_rekening_bkm.upload');

    Route::get('/automasi/bkm',                        [AutomasiController::class, 'bkmIndex'])->name('automasi.bkm.index');
    Route::post('/automasi/bkm/upload',                 [AutomasiController::class, 'uploadBkm'])->name('automasi.bkm.upload');
    Route::post('/automasi/bkm/upload-saldo',           [AutomasiController::class, 'uploadSaldoBkm'])->name('automasi.bkm.upload_saldo');
    Route::post('/automasi/bkm/hitung-saldo',           [AutomasiController::class, 'hitungUlangSaldoBkm'])->name('automasi.bkm.hitung_saldo');

    // Log Aktivitas & User Management
    Route::get('/logs',                 [LogController::class, 'index'])->name('logs.index');
    Route::get('/users',                [UserController::class, 'index'])->name('users.index');
    Route::post('/users',               [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}',       [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',      [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}', function () { return redirect()->route('users.index'); });

    // Keamanan & Buka Blokir Device (Direktur, Direktur Operasional, Project Manager)
    Route::get('/device-lockouts',               [DeviceLockoutController::class, 'index'])->name('device_lockouts.index');
    Route::post('/device-lockouts/{id}/unblock', [DeviceLockoutController::class, 'unblock'])->name('device_lockouts.unblock');
    Route::get('/buka-blokir',                   [DeviceLockoutController::class, 'index'])->name('buka_blokir.index');
    Route::post('/buka-blokir/{id}/unblock',     [DeviceLockoutController::class, 'unblock'])->name('buka_blokir.unblock');
});

// Fallback Route for Uploaded Storage Files (Images/PDFs)
Route::get('/storage/{path}', [StorageFileController::class, 'show'])->where('path', '.*')->name('storage.show');
Route::get('/logistik-file/{path}', [StorageFileController::class, 'show'])->where('path', '.*')->name('logistik.file');

