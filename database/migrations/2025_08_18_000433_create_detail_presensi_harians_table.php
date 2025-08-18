<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detail_presensi_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_harian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->dateTime('waktu_presensi');
            $table->string('keterangan_waktu')->nullable(); // Cth: "-15 menit" atau "+1 jam 5 menit"
            $table->enum('status', ['hadir', 'sakit', 'izin'])->default('hadir');
            $table->timestamps();
            $table->unique(['presensi_harian_id', 'siswa_id']); // Siswa hanya bisa presensi sekali per hari
        });
    }
    public function down(): void {
        Schema::dropIfExists('detail_presensi_harians');
    }
};