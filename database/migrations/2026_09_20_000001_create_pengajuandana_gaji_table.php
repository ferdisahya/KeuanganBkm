<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuandana_gaji', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('uraian');
            $table->decimal('total', 20, 2)->default(0);
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuandana_gaji');
    }
};
