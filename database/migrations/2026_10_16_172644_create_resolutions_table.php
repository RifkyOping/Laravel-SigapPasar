<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->unique()->constrained('issues')->onDelete('cascade');
            $table->foreignId('ditutup_by')->constrained('users')->onDelete('restrict');

            $table->decimal('harga_sebelum_intervensi', 15, 2)->nullable();
            $table->decimal('harga_setelah_intervensi', 15, 2)->nullable();
            $table->integer('durasi_penanganan_hari')->nullable()->comment('SLA: days from issue creation to closure');
            $table->text('kesimpulan');
            $table->text('catatan_akhir')->nullable();
            $table->enum('status_akhir', ['selesai', 'ditutup_hoaks', 'ditutup_tanpa_intervensi'])->default('selesai');

            $table->dateTime('tanggal_penutupan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
