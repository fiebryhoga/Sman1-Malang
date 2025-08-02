<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@sekolah.app', 'role' => 'Super Admin'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@sekolah.app', 'role' => 'Kepala Sekolah'],
            ['name' => 'Waka Kesiswaan', 'email' => 'waka@sekolah.app', 'role' => 'Waka'],
            ['name' => 'Guru Matematika', 'email' => 'gurumapel@sekolah.app', 'role' => 'Guru Mapel'],
            ['name' => 'Guru Olahraga & Basket', 'email' => 'guruekstra@sekolah.app', 'role' => 'Guru Mapel & Ekstra'],
            ['name' => 'Wali Kelas 12A', 'email' => 'wali@sekolah.app', 'role' => 'Guru Wali Kelas'],
            ['name' => 'Guru Bimbingan Konseling', 'email' => 'gurubk@sekolah.app', 'role' => 'Guru BK'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'), // Password default untuk semua
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}
