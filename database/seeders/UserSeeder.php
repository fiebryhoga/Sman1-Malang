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
        // Buat Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.app'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Buat Wali Kelas
        $waliKelasUser = User::firstOrCreate(
            ['email' => 'wali@sekolah.app'],
            [
                'name' => 'Budi Santoso (Wali Kelas)',
                'password' => Hash::make('password'),
            ]
        );
        $waliKelasUser->assignRole('Guru Wali Kelas');



        $guruMapel = User::firstOrCreate(
                ['email' => 'gurumapel@sekolah.app'],
                [
                    'name' => 'Rina Marlina (Guru Mapel)',
                    'password' => Hash::make('password'),
                ]
            );
            $guruMapel->assignRole('Guru Mata Pelajaran'); // Pastikan role ini ada

        // Tugaskan user tersebut sebagai wali di kelas pertama yang ditemukan
        $kelasPertama = Kelas::first();
        if ($kelasPertama) {
            $kelasPertama->update(['wali_kelas_id' => $waliKelasUser->id]);
        }
    }
}
