<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');

            $table->enum('jenis', ['screenshot', 'foto_lapangan', 'dokumen', 'video', 'lainnya']);
            $table->enum('tahap', ['input', 'verifikasi', 'lapangan', 'intervensi', 'resolusi']);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50)->nullable();
            $table->integer('file_size')->nullable()->comment('Size in bytes');
            $table->string('deskripsi')->nullable();
            $table->json('metadata')->nullable()->comment('GPS, timestamp, watermark info');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidences');
    }
};
