<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinary_point_records', function (Blueprint $table) {
            $table->index('siswa_id');
            $table->index('disciplinary_point_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_point_records', function (Blueprint $table) {
            $table->dropIndex(['siswa_id']);
            $table->dropIndex(['disciplinary_point_category_id']);
        });
    }
};