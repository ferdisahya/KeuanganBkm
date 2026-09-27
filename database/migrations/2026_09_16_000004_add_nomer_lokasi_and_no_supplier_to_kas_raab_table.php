<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_raab', function (Blueprint $table) {
            if (!Schema::hasColumn('kas_raab', 'nomer_lokasi')) {
                $table->string('nomer_lokasi')->nullable()->after('subrekening_raab_id');
            }
            if (!Schema::hasColumn('kas_raab', 'no_supplier')) {
                $table->string('no_supplier')->nullable()->after('nomer_lokasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kas_raab', function (Blueprint $table) {
            if (Schema::hasColumn('kas_raab', 'no_supplier')) {
                $table->dropColumn('no_supplier');
            }
            if (Schema::hasColumn('kas_raab', 'nomer_lokasi')) {
                $table->dropColumn('nomer_lokasi');
            }
        });
    }
};
