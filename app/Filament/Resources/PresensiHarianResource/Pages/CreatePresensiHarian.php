<?php

namespace App\Filament\Resources\PresensiHarianResource\Pages;

use App\Filament\Resources\PresensiHarianResource;
use App\Models\Siswa;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePresensiHarian extends CreateRecord
{
    protected static string $resource = PresensiHarianResource::class;

    /**
     * Fungsi ini berjalan setelah record PresensiHarian berhasil dibuat.
     */
    protected function afterCreate(): void
    {
        // Ambil record presensi yang baru saja dibuat
        $presensiHarian = $this->getRecord();
        
        // ✅ PERBAIKAN: Hapus filter 'is_active' dan ambil semua siswa
        $siswas = Siswa::all();

        $detailPresensiData = [];
        foreach ($siswas as $siswa) {
            $detailPresensiData[] = [
                'presensi_harian_id' => $presensiHarian->id,
                'siswa_id' => $siswa->id,
                'status' => 'alpha', // Set semua siswa menjadi alpha secara default
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Masukkan semua data detail sekaligus untuk efisiensi
        if (!empty($detailPresensiData)) {
            DB::table('detail_presensi_harians')->insert($detailPresensiData);
        }
    }
}