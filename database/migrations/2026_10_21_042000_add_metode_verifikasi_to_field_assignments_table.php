<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_assignments', function (Blueprint $table) {
            $table->string('metode_verifikasi', 20)->default('offline')->after('status');
            $table->string('no_telepon_sumber', 50)->nullable()->after('longitude');
            $table->string('sosmed_sumber', 255)->nullable()->after('no_telepon_sumber');
        });
    }

    public function down(): void
    {
        Schema::table('field_assignments', function (Blueprint $table) {
            $table->dropColumn(['metode_verifikasi', 'no_telepon_sumber', 'sosmed_sumber']);
        });
    }
};
