<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hapus tabel issue_verifications terlebih dahulu agar foreign key ke users terlepas
        if (Schema::hasTable('issue_verifications')) {
            Schema::dropIfExists('issue_verifications');
        }

        // 2. Hapus kolom verifikasi_by pada tabel issues jika di MySQL
        if (DB::getDriverName() === 'mysql' && Schema::hasColumn('issues', 'verifikasi_by')) {
            Schema::table('issues', function (Blueprint $table) {
                try {
                    $table->dropForeign(['verifikasi_by']);
                } catch (\Exception $e) {}
                $table->dropColumn('verifikasi_by');
            });
        }

        // 3. Tangani riwayat issue_logs yang terikat dengan user verifikator
        $verifikatorIds = DB::table('users')->where('role', 'verifikator')->pluck('id');
        $adminId = DB::table('users')->whereIn('role', ['super_admin', 'admin'])->value('id');

        if ($verifikatorIds->isNotEmpty()) {
            if ($adminId) {
                DB::table('issue_logs')->whereIn('user_id', $verifikatorIds)->update(['user_id' => $adminId]);
            } else {
                DB::table('issue_logs')->whereIn('user_id', $verifikatorIds)->delete();
            }
            // Hapus user verifikator
            DB::table('users')->whereIn('id', $verifikatorIds)->delete();
        }

        // 4. Ubah ENUM role pada tabel users tanpa 'verifikator' jika di MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pimpinan', 'super_admin', 'admin', 'pengawas_siber', 'petugas_lapangan') NOT NULL DEFAULT 'pengawas_siber'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pimpinan', 'super_admin', 'admin', 'pengawas_siber', 'verifikator', 'petugas_lapangan') NOT NULL DEFAULT 'pengawas_siber'");
        }

        if (!Schema::hasColumn('issues', 'verifikasi_by')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->foreignId('verifikasi_by')->nullable()->constrained('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('issue_verifications')) {
            Schema::create('issue_verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
                $table->foreignId('verifikator_id')->constrained('users')->onDelete('restrict');
                $table->enum('hasil_verifikasi', ['valid', 'butuh_cek_lapangan', 'hoaks', 'duplikasi'])->default('valid');
                $table->enum('prioritas_rekomendasi', ['rendah', 'sedang', 'tinggi', 'kritis'])->nullable();
                $table->text('catatan_verifikasi');
                $table->text('saran_tindakan')->nullable();
                $table->timestamp('tanggal_verifikasi')->useCurrent();
                $table->foreignId('duplikasi_dari')->nullable()->constrained('issues')->onDelete('set null');
                $table->timestamps();
            });
        }
    }
};
