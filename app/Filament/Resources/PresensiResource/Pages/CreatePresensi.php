<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon; // Pastikan use Carbon ada
use Illuminate\Support\Facades\DB;

class CreatePresensi extends CreateRecord
{
    protected static string $resource = PresensiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil ID jadwal dari form
        $jadwalId = $data['jadwal_id'];
        $jadwal = DB::table('kelas_mata_pelajaran')->find($jadwalId);

        // Tambahkan data yang benar ke array yang akan disimpan
        $data['kelas_id'] = $jadwal->kelas_id;
        $data['mata_pelajaran_id'] = $jadwal->mata_pelajaran_id;
        $data['guru_id'] = $jadwal->user_id;
        unset($data['jadwal_id']);
        
        // PERUBAHAN DI SINI: Menyimpan hari dalam Bahasa Indonesia
        $hari = Carbon::parse($data['tanggal'])
                      ->locale('id') // <-- Paksa menggunakan Bahasa Indonesia
                      ->translatedFormat('l'); // 'l' adalah format untuk nama hari lengkap
                      
        $data['hari'] = $hari;

        return $data;
    }
}