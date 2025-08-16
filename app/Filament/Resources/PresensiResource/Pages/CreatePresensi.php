<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use App\Models\JadwalMengajar;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreatePresensi extends CreateRecord
{
    protected static string $resource = PresensiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $jadwalId = $data['jadwal_id'];
        $jadwal = JadwalMengajar::find($jadwalId);
        
        if (!$jadwal) {
            return parent::mutateFormDataBeforeCreate($data);
        }

        $data['kelas_id'] = $jadwal->kelas_id;
        $data['mata_pelajaran_id'] = $jadwal->mata_pelajaran_id;
        $data['guru_id'] = $jadwal->user_id;

        // unset($data['jadwal_id']);
        
        $hari = Carbon::parse($data['tanggal'])
                      ->locale('id')
                      ->translatedFormat('l');
                      
        $data['hari'] = $hari;

        return $data;
    }
}