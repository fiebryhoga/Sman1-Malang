<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah tabel 'disciplinary_point_categories' menjadi 'pelanggarans'
        Schema::rename('disciplinary_point_categories', 'pelanggarans');
        Schema::table('pelanggarans', function (Blueprint $table) {
            $table->dropColumn('points'); // Hapus kolom poin
            $table->string('kode', 10)->after('id'); // Tambah kolom kode (A1, B2, dst)
            $table->string('grup', 1)->after('kode'); // Tambah kolom grup (A, B, C)
            $table->renameColumn('name', 'deskripsi'); // Ganti nama kolom 'name' menjadi 'deskripsi'
        });

        // 2. Ubah tabel 'disciplinary_point_records' menjadi 'pelanggaran_records'
        Schema::table('disciplinary_point_records', function (Blueprint $table) {
            // Hapus foreign key lama dulu
            $table->dropForeign('dpr_category_fk');
        });
        Schema::rename('disciplinary_point_records', 'pelanggaran_records');
        Schema::table('pelanggaran_records', function (Blueprint $table) {
            $table->renameColumn('disciplinary_point_category_id', 'pelanggaran_id');
            $table->text('catatan')->nullable()->after('photo'); // Tambah kolom catatan

            // Buat ulang foreign key ke tabel yang baru
            $table->foreign('pelanggaran_id')->references('id')->on('pelanggarans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Logika untuk mengembalikan perubahan jika di-rollback
        Schema::table('pelanggaran_records', function (Blueprint $table) {
            $table->dropForeign(['pelanggaran_id']);
            $table->dropColumn('catatan');
            $table->renameColumn('pelanggaran_id', 'disciplinary_point_category_id');
        });
        Schema::rename('pelanggaran_records', 'disciplinary_point_records');
        Schema::table('disciplinary_point_records', function (Blueprint $table) {
            $table->foreign('disciplinary_point_category_id', 'dpr_category_fk')->references('id')->on('disciplinary_point_categories')->cascadeOnDelete();
        });

        Schema::table('pelanggarans', function (Blueprint $table) {
            $table->renameColumn('deskripsi', 'name');
            $table->dropColumn(['kode', 'grup']);
            $table->integer('points')->default(0);
        });
        Schema::rename('pelanggarans', 'disciplinary_point_categories');
    }
};