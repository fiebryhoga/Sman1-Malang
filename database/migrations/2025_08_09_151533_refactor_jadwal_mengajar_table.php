<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mengajar_jam_pelajaran', function (Blueprint $table) {
            $table->foreignId('jadwal_mengajar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jam_pelajaran_id')->constrained('jam_pelajarans')->cascadeOnDelete();
            $table->primary(['jadwal_mengajar_id', 'jam_pelajaran_id'], 'jmj_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajar_jam_pelajaran');
    }
};