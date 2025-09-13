<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ PERBAIKAN: Menggunakan 'nip' sebagai kunci utama dan menambahkan datanya
        $superAdmin = User::firstOrCreate(
            ['nip' => '000000001'], // Kunci pencarian
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@sekolah.app', // Email tetap bisa diisi
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $waliKelasUser = User::firstOrCreate(
            ['nip' => '198501012010011001'], // Kunci pencarian
            [
                'name' => 'Budi Santoso (Wali Kelas)',
                'password' => Hash::make('password'),
                // Email dikosongkan untuk menunjukkan bahwa ini opsional
            ]
        );
        $waliKelasUser->assignRole('Guru Wali Kelas');

        $guruMapel = User::firstOrCreate(
            ['nip' => '199002022015022002'], // Kunci pencarian
            [
                'name' => 'Rina Marlina (Guru Mapel)',
                'password' => Hash::make('password'),
            ]
        );
        $guruMapel->assignRole('Guru Mata Pelajaran');

        // Logika untuk menjadikan Budi Santoso sebagai wali kelas pertama tetap sama
        $kelasPertama = Kelas::first();
        if ($kelasPertama) {
            $kelasPertama->update(['wali_kelas_id' => $waliKelasUser->id]);
        }
    }
}
