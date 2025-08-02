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
        // dalam method up()
Schema::table('kelas', function (Blueprint $table) {
    // Relasi ke tabel users, bisa null jika belum ada wali kelas
    $table->foreignId('wali_kelas_id')->nullable()->after('nama')->constrained('users')->nullOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            //
        });
    }
};
