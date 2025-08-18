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
        Schema::table('detail_presensi_harians', function (Blueprint $table) {
            // Mengubah kolom agar boleh kosong (nullable)
            $table->dateTime('waktu_presensi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_presensi_harians', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula (wajib diisi) jika di-rollback
            $table->dateTime('waktu_presensi')->nullable(false)->change();
        });
    }
};