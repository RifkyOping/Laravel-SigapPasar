<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->foreignId('petugas_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict');

            $table->dateTime('tanggal_penugasan');
            $table->dateTime('tanggal_kunjungan')->nullable();
            $table->enum('status', ['ditugaskan', 'sedang_dikerjakan', 'selesai'])->default('ditugaskan');

            // Hasil Lapangan
            $table->text('catatan_temuan')->nullable();
            $table->string('foto_bukti')->nullable()->comment('Path to uploaded photo');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('harga_aktual', 15, 2)->nullable()->comment('Harga temuan di lapangan');
            $table->text('catatan_wawancara')->nullable();
            $table->dateTime('checkin_at')->nullable();

            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_assignments');
    }
};
