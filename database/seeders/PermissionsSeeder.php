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

        $permissions = [
            'melihat_pengguna', 'mengelola_pengguna',
            'melihat_peran', 'mengelola_peran',
            'melihat_tahun_ajaran', 'mengelola_tahun_ajaran',
            'melihat_kelas', 'mengelola_kelas',
            'melihat_siswa', 'mengelola_siswa',
            'melihat_mata_pelajaran', 'mengelola_mata_pelajaran',
            // Hak akses presensi mapel
            'melihat_presensi_diampu',
            'mengelola_presensi_diampu',
            'melihat_presensi_semua',
            'mengelola_presensi_semua',
            'mengelola presensi harian',
            'mengelola_pelanggaran', 
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $waliKelas = Role::firstOrCreate(['name' => 'Guru Wali Kelas']);
        $waliKelas->givePermissionTo([
            'melihat_kelas', 
            'melihat_siswa', 
            'melihat_presensi_diampu', 
            'mengelola_presensi_diampu'
        ]);

        $guruMapel = Role::firstOrCreate(['name' => 'Guru Mata Pelajaran']);
        $guruMapel->givePermissionTo([
            'melihat_kelas', 
            'melihat_mata_pelajaran', 
            'melihat_presensi_diampu', 
            'mengelola_presensi_diampu'
        ]);
    }
}