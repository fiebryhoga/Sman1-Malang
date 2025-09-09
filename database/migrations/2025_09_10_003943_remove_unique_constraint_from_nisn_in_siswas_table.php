<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Menghapus aturan unik dari kolom 'nisn'
            // 'siswas_nisn_unique' adalah nama default yang dibuat Laravel
            $table->dropUnique('siswas_nisn_unique');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Menambahkan kembali aturan unik jika migrasi di-rollback
            $table->unique('nisn');
        });
    }
};