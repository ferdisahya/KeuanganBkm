<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add jenis_transaksi to kas_bkm
        Schema::table('kas_bkm', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->after('uraian')->nullable();
            $table->decimal('nominal', 20, 2)->after('jenis_transaksi')->default(0);
        });

        // Add jenis_transaksi to kas_raab
        Schema::table('kas_raab', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->after('uraian')->nullable();
            $table->decimal('nominal', 20, 2)->after('jenis_transaksi')->default(0);
        });

        // Migrate existing data for kas_bkm
        DB::statement("
            UPDATE kas_bkm 
            SET jenis_transaksi = CASE 
                WHEN COALESCE(debet, 0) > 0 THEN 'debit'
                WHEN COALESCE(kredit, 0) > 0 THEN 'kredit'
                ELSE 'debit'
            END,
            nominal = CASE 
                WHEN COALESCE(debet, 0) > 0 THEN debet
                WHEN COALESCE(kredit, 0) > 0 THEN kredit
                ELSE 0
            END
        ");

        // Migrate existing data for kas_raab
        DB::statement("
            UPDATE kas_raab 
            SET jenis_transaksi = CASE 
                WHEN COALESCE(debet, 0) > 0 THEN 'debit'
                WHEN COALESCE(kredit, 0) > 0 THEN 'kredit'
                ELSE 'debit'
            END,
            nominal = CASE 
                WHEN COALESCE(debet, 0) > 0 THEN debet
                WHEN COALESCE(kredit, 0) > 0 THEN kredit
                ELSE 0
            END
        ");

        // Make jenis_transaksi NOT NULL after migration
        Schema::table('kas_bkm', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable(false)->change();
        });

        Schema::table('kas_raab', function (Blueprint $table) {
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas_bkm', function (Blueprint $table) {
            $table->dropColumn(['jenis_transaksi', 'nominal']);
        });

        Schema::table('kas_raab', function (Blueprint $table) {
            $table->dropColumn(['jenis_transaksi', 'nominal']);
        });
    }
};
