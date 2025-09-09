<?php

namespace App\Filament\Resources\EkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEkstrakurikuler extends EditRecord
{
    protected static string $resource = EkstrakurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ 5. Tambahkan tombol hapus yang hanya terlihat oleh Super Admin
            Actions\DeleteAction::make()
                ->visible(fn (): bool => auth()->user()->hasRole('Super Admin')),
        ];
    }
}
