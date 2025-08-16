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
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.app'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

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
            $guruMapel->assignRole('Guru Mata Pelajaran'); 

        $kelasPertama = Kelas::first();
        if ($kelasPertama) {
            $kelasPertama->update(['wali_kelas_id' => $waliKelasUser->id]);
        }
    }
}
