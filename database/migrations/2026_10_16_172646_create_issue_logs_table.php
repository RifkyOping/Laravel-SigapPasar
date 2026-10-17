<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained('issues')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            $table->string('action');
            $table->string('status_dari')->nullable();
            $table->string('status_ke')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('data_perubahan')->nullable()->comment('Snapshot of changed data');
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['issue_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_logs');
    }
};
