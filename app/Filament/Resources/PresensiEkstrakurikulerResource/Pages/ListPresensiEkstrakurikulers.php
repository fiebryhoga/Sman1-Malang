<?php

namespace App\Filament\Resources\PresensiEkstrakurikulerResource\Pages;

use App\Filament\Resources\PresensiEkstrakurikulerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresensiEkstrakurikulers extends ListRecords
{
    protected static string $resource = PresensiEkstrakurikulerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
