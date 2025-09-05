<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            // Menambahkan aturan bahwa setiap nilai di kolom 'kode' harus unik
            $table->unique('kode');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggarans', function (Blueprint $table) {
            // Menghapus aturan unik jika migrasi di-rollback
            $table->dropUnique(['kode']);
        });
    }
};