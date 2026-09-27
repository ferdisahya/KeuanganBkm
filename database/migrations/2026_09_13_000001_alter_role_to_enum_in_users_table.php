<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE users SET role = 'Project Manager' WHERE LOWER(role) = 'project manager'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Direktur', 'Direktur Operasional', 'Project Manager', 'Admin Keuangan') NOT NULL DEFAULT 'Admin Keuangan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'Admin Keuangan'");
    }
};
