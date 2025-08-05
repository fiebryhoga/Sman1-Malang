<?php

namespace App\Filament\Resources\DisciplinaryPointRecordResource\Pages;

use App\Filament\Resources\DisciplinaryPointRecordResource;
use App\Jobs\SendDisciplinaryNotification;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDisciplinaryPointRecord extends CreateRecord
{
    protected static string $resource = DisciplinaryPointRecordResource::class;

    protected function afterCreate(): void
    {
        // Ambil record yang baru dibuat
        $record = $this->record;

        // Ambil total poin terbaru
        $totalPoints = $record->siswa->disciplinary_points;

        // Kirim job ke antrian dengan total poin yang sudah dihitung
        SendDisciplinaryNotification::dispatch($record->id, $totalPoints);

        Notification::make()
            ->title('Catatan berhasil disimpan!')
            ->body('Notifikasi akan segera dikirim ke orang tua di latar belakang.')
            ->success()
            ->send();
    }
}