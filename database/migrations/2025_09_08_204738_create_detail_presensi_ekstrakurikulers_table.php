<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_presensi_ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            
            // ✅ PERBAIKAN: Beri nama foreign key secara manual dengan nama yang lebih pendek
            $table->foreignId('presensi_ekstrakurikuler_id')
                  ->constrained('presensi_ekstrakurikulers')
                  ->cascadeOnDelete()
                  ->name('detail_presensi_ekstra_foreign'); // Nama kustom yang lebih pendek

            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_presensi_ekstrakurikulers');
    }
};