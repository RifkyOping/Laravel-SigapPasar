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
        Schema::create('commodity_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komoditas_id')->nullable()->constrained('master_data')->nullOnDelete();
            $table->string('nama_variant');
            $table->string('satuan')->default('kg');
            $table->date('tanggal');
            $table->decimal('harga', 14, 2);
            $table->string('sumber')->default('import_excel');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['nama_variant', 'tanggal'], 'unique_variant_tanggal');
            $table->index(['tanggal', 'komoditas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_prices');
    }
};
