<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['pimpinan', 'super_admin', 'admin', 'pengawas_siber', 'verifikator', 'petugas_lapangan'])
                  ->default('pengawas_siber')
                  ->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('nip')->nullable()->after('phone');
            $table->string('instansi')->nullable()->after('nip');
            $table->boolean('is_active')->default(true)->after('instansi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'nip', 'instansi', 'is_active']);
        });
    }
};
