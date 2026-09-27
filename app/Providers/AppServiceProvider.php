<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        View::composer('layouts.app', function ($view) {
            $unreadCounts = [];
            
            if (auth()->check()) {
                $userId = auth()->id();
                $userCreatedAt = auth()->user()->created_at ?? now()->subDays(30);
                $routeName = request()->route() ? request()->route()->getName() : '';

                // Map routes to page_key
                $pageKeyMap = [
                    'kas_bkm' => ['kas_bkm.index', 'kas_bkm.create', 'kas_bkm.edit'],
                    'kas_raab' => ['kas_raab.index', 'kas_raab.create', 'kas_raab.edit'],
                    'sisa_anggaran' => ['sisa_anggaran.index', 'sisa_anggaran.create', 'sisa_anggaran.edit'],
                    'pengajuan_dana_bkm' => ['pengajuan_dana_bkm.index', 'pengajuan_dana_bkm.create', 'pengajuan_dana_bkm.edit'],
                    'pengajuan_dana_raab' => ['pengajuan_dana_raab.index', 'pengajuan_dana_raab.create', 'pengajuan_dana_raab.edit'],
                    'pengajuan_dana_diluar_operasional' => ['pengajuan_dana_diluar_operasional.index', 'pengajuan_dana_diluar_operasional.create', 'pengajuan_dana_diluar_operasional.edit'],
                    'rekening_bkm' => ['master.rekening_bkm.index'],
                    'rekening_raab' => ['master.rekening_raab.index'],
                    'subrekening_bkm' => ['master.subrekening_bkm.index'],
                    'subrekening_raab' => ['master.subrekening_raab.index'],
                    'item_bkm' => ['master.item_bkm.index'],
                    'penyaluran_bkm' => ['master.penyaluran_pembayaran_bkm.index'],
                    'penyaluran_raab' => ['master.penyaluran_pembayaran_raab.index'],
                    'pembelian_barang' => ['logistik.pembelian_barang'],
                    'kebutuhan_langsung' => ['logistik.kebutuhan_langsung'],
                ];

                $currentPageKey = null;
                foreach ($pageKeyMap as $key => $routes) {
                    if (in_array($routeName, $routes)) {
                        $currentPageKey = $key;
                        break;
                    }
                }

                try {
                    // Update last_viewed_at if user is on a tracked page in GET request
                    if ($currentPageKey && request()->isMethod('GET')) {
                        DB::table('user_page_views')->updateOrInsert(
                            ['user_id' => $userId, 'page_key' => $currentPageKey],
                            ['last_viewed_at' => now()]
                        );
                    }

                    // Get user's page views
                    $userViews = DB::table('user_page_views')
                        ->where('user_id', $userId)
                        ->pluck('last_viewed_at', 'page_key')
                        ->toArray();

                    $modelsMap = [
                        'kas_bkm' => \App\Models\KasBkm::class,
                        'kas_raab' => \App\Models\KasRaab::class,
                        'sisa_anggaran' => \App\Models\SisaAnggaranBkm::class,
                        'pengajuan_dana_bkm' => \App\Models\PengajuanDanaBkm::class,
                        'pengajuan_dana_raab' => \App\Models\PengajuanDanaRaab::class,
                        'pengajuan_dana_diluar_operasional' => \App\Models\PengajuanDanaDiluarOperasional::class,
                        'rekening_bkm' => \App\Models\RekeningBkm::class,
                        'rekening_raab' => \App\Models\RekeningRaab::class,
                        'subrekening_bkm' => \App\Models\SubrekeningBkm::class,
                        'subrekening_raab' => \App\Models\SubrekeningRaab::class,
                        'item_bkm' => \App\Models\ItemBkm::class,
                        'penyaluran_bkm' => \App\Models\PenyaluranPembayaranBkm::class,
                        'penyaluran_raab' => \App\Models\PenyaluranPembayaranRaab::class,
                        'pembelian_barang' => \App\Models\Pembelian::class,
                        'kebutuhan_langsung' => \App\Models\KebutuhanLangsung::class,
                    ];

                    foreach ($modelsMap as $key => $modelClass) {
                        try {
                            $dateCol = 'created_at';

                            if (isset($userViews[$key])) {
                                $lastViewed = $userViews[$key];
                                $cnt = $modelClass::where($dateCol, '>', $lastViewed)->count();
                            } else {
                                // If user never opened this page before, use user creation time
                                $lastViewed = $userCreatedAt;
                                $cnt = $modelClass::where($dateCol, '>', $lastViewed)->count();
                            }
                            $unreadCounts[$key] = $cnt > 99 ? '99+' : $cnt;
                        } catch (\Throwable $ex) {
                            $unreadCounts[$key] = 0;
                        }
                    }
                } catch (\Throwable $e) {
                    $unreadCounts = [];
                }
            }

            $view->with('unreadCounts', $unreadCounts);
        });
    }
}
