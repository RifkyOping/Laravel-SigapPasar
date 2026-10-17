<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'lokasi_spesifik')) {
                $table->string('lokasi_spesifik')->nullable()->after('pasar_id');
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE issues MODIFY COLUMN kategori_isu VARCHAR(100) NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'lokasi_spesifik')) {
                $table->dropColumn('lokasi_spesifik');
            }
        });
    }
};
