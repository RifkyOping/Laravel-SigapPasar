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
        Schema::table('commodity_prices', function (Blueprint $table) {
            $table->foreignId('kabupaten_id')->nullable()->after('komoditas_id')->constrained('master_data')->nullOnDelete();
            $table->string('nama_kabupaten')->nullable()->after('kabupaten_id');
        });

        Schema::table('commodity_prices', function (Blueprint $table) {
            $table->dropUnique('unique_variant_tanggal');
            $table->index(['nama_variant', 'tanggal', 'kabupaten_id'], 'idx_variant_tanggal_kabupaten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodity_prices', function (Blueprint $table) {
            $table->dropIndex('idx_variant_tanggal_kabupaten');
            $table->unique(['nama_variant', 'tanggal'], 'unique_variant_tanggal');
            $table->dropForeign(['kabupaten_id']);
            $table->dropColumn(['kabupaten_id', 'nama_kabupaten']);
        });
    }
};
