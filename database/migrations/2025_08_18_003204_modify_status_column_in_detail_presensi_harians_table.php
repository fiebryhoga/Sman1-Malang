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
            // Mengubah kolom status untuk menyertakan 'alpha'
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_presensi_harians', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika di-rollback
            $table->enum('status', ['hadir', 'sakit', 'izin'])->default('hadir')->change();
        });
    }
};