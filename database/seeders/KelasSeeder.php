<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat
        Kelas::query()->delete();

        // Ambil tahun ajaran yang aktif
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Definisikan 10 kelas dengan format yang diinginkan
        $daftarKelas = [
            'X - TKJ - A', 'X - TKJ - B',
            'X - RPL - A', 'XI - RPL - B',
            'XI - MM - A', 'XI - MM - B',
            'XII - TKJ - A', 'XII - TKJ - B',
            'XII - RPL - A', 'XII - MM - A'
        ];

        foreach ($daftarKelas as $namaKelas) {
            Kelas::create([
                'nama' => $namaKelas,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }
    }
}
