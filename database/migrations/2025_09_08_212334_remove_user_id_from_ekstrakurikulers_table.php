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
        // ...remove_user_id_from_ekstrakurikulers_table.php
Schema::table('ekstrakurikulers', function (Blueprint $table) {
    // Hapus foreign key dulu sebelum menghapus kolomnya
    $table->dropForeign(['user_id']);
    $table->dropColumn('user_id');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikulers', function (Blueprint $table) {
            //
        });
    }
};
