<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['komoditas', 'kabupaten', 'pasar']);
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('satuan')->nullable()->comment('Satuan ukuran untuk komoditas (kg, liter, dll)');
            $table->decimal('harga_referensi', 15, 2)->nullable()->comment('Harga Eceran Tertinggi / HET');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
