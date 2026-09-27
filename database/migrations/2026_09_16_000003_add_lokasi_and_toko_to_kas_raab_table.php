<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_raab', function (Blueprint $table) {
            if (!Schema::hasColumn('kas_raab', 'lokasi_id')) {
                $table->unsignedBigInteger('lokasi_id')->nullable()->after('subrekening_raab_id');
            }
            if (!Schema::hasColumn('kas_raab', 'toko_id')) {
                $table->unsignedBigInteger('toko_id')->nullable()->after('lokasi_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kas_raab', function (Blueprint $table) {
            if (Schema::hasColumn('kas_raab', 'toko_id')) {
                $table->dropColumn('toko_id');
            }
            if (Schema::hasColumn('kas_raab', 'lokasi_id')) {
                $table->dropColumn('lokasi_id');
            }
        });
    }
};
