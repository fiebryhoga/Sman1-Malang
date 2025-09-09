<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'nis'
            $table->string('nisn')->unique()->nullable()->after('nis');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->year('angkatan')->nullable()->after('agama');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Logika untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['nisn', 'agama', 'angkatan']);
        });
    }
};