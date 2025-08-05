<?php
// database/migrations/xxxx_xx_xx_create_disciplinary_point_records_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_point_records', function (Blueprint $table) {
            $table->id();
            // Berikan nama foreign key yang lebih pendek secara manual
            $table->foreignId('siswa_id')->constrained('siswas', 'id', 'dpr_siswa_fk')->cascadeOnDelete();
            $table->foreignId('disciplinary_point_category_id')->constrained('disciplinary_point_categories', 'id', 'dpr_category_fk')->cascadeOnDelete();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_point_records');
    }
};