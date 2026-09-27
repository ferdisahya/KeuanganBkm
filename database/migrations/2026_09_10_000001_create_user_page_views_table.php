<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('page_key', 50);
            $table->timestamp('last_viewed_at')->useCurrent()->useCurrentOnUpdate();
            $table->unique(['user_id', 'page_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_page_views');
    }
};
