<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pimpinan', 'super_admin', 'admin', 'pengawas_siber', 'verifikator', 'petugas_lapangan') NOT NULL DEFAULT 'pengawas_siber'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'pengawas_siber', 'verifikator', 'petugas_lapangan') NOT NULL DEFAULT 'pengawas_siber'");
        }
    }
};
