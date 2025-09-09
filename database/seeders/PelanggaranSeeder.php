<?php

namespace Database\Seeders;

use App\Models\Pelanggaran;
use Illuminate\Database\Seeder;

class PelanggaranSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggarans = [
            ['grup' => 'A', 'kode' => 'A1', 'deskripsi' => 'Datang terlambat'],
            ['grup' => 'A', 'kode' => 'A2', 'deskripsi' => 'Atribut tidak lengkap'],
            ['grup' => 'A', 'kode' => 'A3', 'deskripsi' => 'Seragam tidak sesuai (termasuk sepatu)'],
            ['grup' => 'A', 'kode' => 'A4', 'deskripsi' => 'Menggunakan rias dan aksesoris melebihi kepatutan'],
            ['grup' => 'A', 'kode' => 'A5', 'deskripsi' => 'Rambut gondrong/tidak rapi/disemir'],
            ['grup' => 'A', 'kode' => 'A6', 'deskripsi' => 'Berada di luar kelas tanpa izin pada jam pembelajaran'],
            ['grup' => 'A', 'kode' => 'A7', 'deskripsi' => 'Tidak memelihara kebersihan lingkungan sekitarnya'],
            ['grup' => 'B', 'kode' => 'B1', 'deskripsi' => 'Menggunakan HP saat kegiatan sekolah'],
            ['grup' => 'B', 'kode' => 'B2', 'deskripsi' => 'Membawa Alat Rias'],
            ['grup' => 'B', 'kode' => 'B3', 'deskripsi' => 'Meninggalkan sekolah tanpa izin'],
            ['grup' => 'B', 'kode' => 'B4', 'deskripsi' => 'Berpacaran'],
            ['grup' => 'B', 'kode' => 'B5', 'deskripsi' => 'Memalsukan tanda tangan orang lain dan atau stempel'],
            ['grup' => 'B', 'kode' => 'B6', 'deskripsi' => 'Membuat dan atau menyebarkan pernyataan bohong/tidak benar'],
            ['grup' => 'B', 'kode' => 'B7', 'deskripsi' => 'Berkata kotor/mengumpat'],
            ['grup' => 'B', 'kode' => 'B8', 'deskripsi' => 'Memainkan permainan kartu pada waktu dan tempat serta dengan tujuan yang tidak tepat'],
            ['grup' => 'C', 'kode' => 'C1', 'deskripsi' => 'Membawa dan menggunakan senjata tajam'],
            ['grup' => 'C', 'kode' => 'C2', 'deskripsi' => 'Membawa dan menggunakan rokok konvensional/elektrik'],
            ['grup' => 'C', 'kode' => 'C3', 'deskripsi' => 'Berkelahi'],
            ['grup' => 'C', 'kode' => 'C4', 'deskripsi' => 'Melompati pagar sekolah'],
            ['grup' => 'C', 'kode' => 'C5', 'deskripsi' => 'Merusak sarana dan prasarana sekolah'],
            ['grup' => 'C', 'kode' => 'C6', 'deskripsi' => 'Melakukan perundungan'],
            ['grup' => 'C', 'kode' => 'C7', 'deskripsi' => 'Mencuri'],
            ['grup' => 'C', 'kode' => 'C8', 'deskripsi' => 'Membawa dan menggunakan miras, narkoba, dan petasan'],
            ['grup' => 'C', 'kode' => 'C9', 'deskripsi' => 'Mencemarkan nama baik sekolah'],
            ['grup' => 'C', 'kode' => 'C10', 'deskripsi' => 'Berbuat asusila'],
            ['grup' => 'C', 'kode' => 'C11', 'deskripsi' => 'Melakukan tindak pidana'],
        ];

        foreach ($pelanggarans as $pelanggaran) {
            // ✅ PERBAIKAN: Gunakan updateOrCreate
            Pelanggaran::updateOrCreate(
                ['kode' => $pelanggaran['kode']], // Cari berdasarkan 'kode'
                $pelanggaran // Data untuk di-update atau dibuat
            );
        }
    }
}