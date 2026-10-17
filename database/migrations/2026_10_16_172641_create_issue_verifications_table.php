<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->foreignId('verifikator_id')->constrained('users')->onDelete('restrict');

            $table->enum('hasil_verifikasi', ['valid', 'butuh_cek_lapangan', 'hoaks', 'duplikasi']);
            $table->enum('prioritas_rekomendasi', ['rendah', 'sedang', 'tinggi', 'kritis'])->nullable();
            $table->text('catatan_verifikasi');
            $table->string('saran_tindakan')->nullable();

            // Duplikasi check
            $table->foreignId('duplikasi_dari')->nullable()->constrained('issues')->onDelete('set null');

            $table->dateTime('tanggal_verifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_verifications');
    }
};
