<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_ajukan_dana_gaji', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('tanggal');
            $table->string('file');
            $table->decimal('nominal', 15, 2)->unsigned();
            $table->text('keterangan')->nullable();
            $table->string('status', 50)->default('Pending');

            // Approval tracking
            $table->dateTime('pm_approved_at')->nullable();
            $table->unsignedBigInteger('pm_approved_by')->nullable();

            $table->dateTime('do_approved_at')->nullable();
            $table->unsignedBigInteger('do_approved_by')->nullable();

            $table->dateTime('dir_approved_at')->nullable();
            $table->unsignedBigInteger('dir_approved_by')->nullable();

            // Rejection tracking
            $table->dateTime('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();

            // Bukti transfer
            $table->string('bukti')->nullable();
            $table->dateTime('bukti_uploaded_at')->nullable();
            $table->unsignedBigInteger('bukti_uploaded_by')->nullable();

            $table->timestamps();

            $table->index('tanggal');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_ajukan_dana_gaji');
    }
};
