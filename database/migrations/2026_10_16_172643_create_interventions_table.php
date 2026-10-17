<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->foreignId('dibuat_by')->constrained('users')->onDelete('restrict');

            $table->enum('jenis_intervensi', ['operasi_pasar', 'sidak_gudang', 'koordinasi_bulog', 'rapat_koordinasi', 'penyuluhan', 'lainnya']);
            $table->string('nama_intervensi');
            $table->text('deskripsi');
            $table->text('hasil_diharapkan')->nullable();

            $table->enum('status', ['direncanakan', 'sedang_berjalan', 'selesai', 'dibatalkan'])->default('direncanakan');

            $table->date('tanggal_mulai');
            $table->date('target_selesai')->nullable();
            $table->date('tanggal_selesai_aktual')->nullable();

            $table->string('pic_nama');
            $table->string('pic_jabatan')->nullable();
            $table->text('instansi_terlibat')->nullable()->comment('JSON array of instansi names');
            $table->text('progres_terakhir')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
