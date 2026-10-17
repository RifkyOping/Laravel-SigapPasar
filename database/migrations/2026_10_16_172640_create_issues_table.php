<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('kode_isu')->unique()->comment('Auto-generated: ISU-YYYYMM-XXXX');

            // Data Isu
            $table->dateTime('tanggal_temuan');
            $table->enum('platform', ['Twitter/X', 'Facebook', 'Instagram', 'TikTok', 'YouTube', 'WhatsApp', 'Online News', 'Lainnya']);
            $table->string('link_postingan')->nullable();
            $table->string('nama_akun')->nullable();
            $table->integer('jumlah_interaksi')->default(0)->comment('Likes + shares + comments');
            $table->integer('jumlah_mention')->default(0);
            $table->text('kutipan_postingan');

            // Kategori & Lokasi
            $table->foreignId('komoditas_id')->constrained('master_data')->onDelete('restrict');
            $table->string('kategori_isu', 100);
            $table->foreignId('pasar_id')->nullable()->constrained('master_data')->onDelete('set null');
            $table->string('lokasi_spesifik')->nullable();
            $table->foreignId('kabupaten_id')->constrained('master_data')->onDelete('restrict');

            // Status & Prioritas
            $table->enum('status', ['baru', 'diverifikasi', 'ditugaskan', 'di_lapangan', 'intervensi', 'selesai', 'ditutup', 'hoaks'])
                  ->default('baru');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('sedang');
            $table->decimal('virality_score', 10, 2)->default(0)->comment('Computed score from interactions');

            // Relasi User
            $table->foreignId('input_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('verifikasi_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assign_by')->nullable()->constrained('users')->onDelete('set null');

            $table->text('catatan_input')->nullable();
            $table->boolean('is_merged')->default(false);
            $table->foreignId('merged_to')->nullable()->constrained('issues')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'prioritas']);
            $table->index(['kabupaten_id', 'tanggal_temuan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
