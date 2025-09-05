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
        // Hapus data lama untuk menghindari duplikat saat seeder dijalankan ulang
        Kelas::query()->delete();

        // Ambil tahun ajaran yang aktif, pastikan ada data tahun ajaran aktif
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaran) {
            $this->command->error('Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan satu terlebih dahulu.');
            return;
        }

        // Definisikan tingkatan kelas dan abjad
        $tingkatan = ['X', 'XI', 'XII'];
        $abjad = range('A', 'K'); // Membuat array dari 'A' sampai 'K'

        // Loop untuk membuat kelas secara otomatis
        foreach ($tingkatan as $tingkat) {
            foreach ($abjad as $huruf) {
                // Gabungkan untuk membuat nama kelas, contoh: "X - A", "XI - K"
                $namaKelas = $tingkat . ' - ' . $huruf;

                Kelas::create([
                    'nama' => $namaKelas,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]);
            }
        }
        
        $this->command->info('Seeder Kelas berhasil dijalankan.');
    }
}
