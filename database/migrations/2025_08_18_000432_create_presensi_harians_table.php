<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('presensi_harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->time('jam_masuk')->default('07:00:00');
            $table->time('batas_presensi')->default('23:59:59');
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('presensi_harians');
    }
};