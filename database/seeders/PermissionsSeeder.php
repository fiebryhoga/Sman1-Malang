<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar semua permissions
        $permissions = [
            'presensi_harian', 'presensi_ekskul', 'presensi_kegiatan',
            'input_poin_pelanggaran', 'lihat_rekap_kelas', 'kirim_pengumuman',
            'buku_penghubung', 'manage_users', 'manage_roles', 'manage_master_data'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // --- BUAT ROLES DAN BERIKAN PERMISSIONS ---

        // Super Admin -> Semua hak akses
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Kepala Sekolah -> Bisa melihat semua dan mengelola beberapa hal
        $kepsek = Role::firstOrCreate(['name' => 'Kepala Sekolah']);
        $kepsek->givePermissionTo(['lihat_rekap_kelas', 'kirim_pengumuman', 'manage_master_data', 'presensi_kegiatan']);

        // Waka -> Fokus pada kegiatan dan pengumuman
        $waka = Role::firstOrCreate(['name' => 'Waka']);
        $waka->givePermissionTo(['lihat_rekap_kelas', 'kirim_pengumuman', 'presensi_kegiatan']);

        // Guru Mapel -> Hanya presensi harian
        $guruMapel = Role::firstOrCreate(['name' => 'Guru Mapel']);
        $guruMapel->givePermissionTo(['presensi_harian']);

        // Guru Mapel & Ekstra -> Presensi harian dan ekskul
        $guruMapelEkstra = Role::firstOrCreate(['name' => 'Guru Mapel & Ekstra']);
        $guruMapelEkstra->givePermissionTo(['presensi_harian', 'presensi_ekskul']);

        // Guru Wali Kelas -> Fokus pada manajemen kelasnya
        $waliKelas = Role::firstOrCreate(['name' => 'Guru Wali Kelas']);
        $waliKelas->givePermissionTo(['presensi_harian', 'lihat_rekap_kelas', 'buku_penghubung', 'input_poin_pelanggaran', 'kirim_pengumuman']);

        // Guru BK -> Fokus pada pelanggaran dan komunikasi
        $guruBk = Role::firstOrCreate(['name' => 'Guru BK']);
        $guruBk->givePermissionTo(['input_poin_pelanggaran', 'buku_penghubung', 'lihat_rekap_kelas']);
    }
}
