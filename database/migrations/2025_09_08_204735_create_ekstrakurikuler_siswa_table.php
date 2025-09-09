<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ekstrakurikuler_siswa', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ekstrakurikuler_id')->constrained()->cascadeOnDelete();
    $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['ekstrakurikuler_id', 'siswa_id']); // Siswa hanya bisa join sekali
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler_siswa');
    }
};
