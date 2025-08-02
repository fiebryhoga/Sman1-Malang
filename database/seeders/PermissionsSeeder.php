<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar permissions dalam Bahasa Indonesia yang disederhanakan
        $permissions = [
            'melihat_pengguna', 'mengelola_pengguna',
            'melihat_peran', 'mengelola_peran',
            'melihat_tahun_ajaran', 'mengelola_tahun_ajaran',
            'melihat_kelas', 'mengelola_kelas',       // Disederhanakan
            'melihat_siswa', 'mengelola_siswa',       // Disederhanakan
            'melihat_mata_pelajaran', 'mengelola_mata_pelajaran', // Baru
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- BUAT ROLES ---

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $waliKelas = Role::firstOrCreate(['name' => 'Guru Wali Kelas']);
        // Sekarang Wali Kelas bisa melihat semua kelas dan siswa,
        // filter akan menangani tampilan "kelas yang diampu"
        $waliKelas->givePermissionTo(['melihat_kelas', 'melihat_siswa']);

        $guruMapel = Role::firstOrCreate(['name' => 'Guru Mata Pelajaran']);
        $guruMapel->givePermissionTo(['melihat_kelas', 'melihat_mata_pelajaran']);
    }
}
