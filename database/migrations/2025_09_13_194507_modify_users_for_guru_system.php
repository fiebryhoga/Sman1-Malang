<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User; // ✅ 1. Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        // Tahap 1: Tambahkan kolom baru sebagai nullable tanpa unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('id');
            $table->string('no_telepon')->nullable()->after('name');
            $table->string('email')->nullable()->unique(false)->change();
        });

        // Tahap 2: Isi NIP unik untuk semua user yang sudah ada
        $users = User::all();
        foreach ($users as $user) {
            // Kita buat NIP unik sementara berdasarkan ID atau email
            $uniqueNip = 'GURU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT);
            $user->update(['nip' => $uniqueNip]);
        }

        // Tahap 3: Sekarang ubah kolom NIP menjadi not nullable dan tambahkan unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable(false)->unique()->change();
        });

        // Sesuaikan tabel password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropPrimary();
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'no_telepon']);
            $table->string('email')->unique()->nullable(false)->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->primary('email');
        });
    }
};