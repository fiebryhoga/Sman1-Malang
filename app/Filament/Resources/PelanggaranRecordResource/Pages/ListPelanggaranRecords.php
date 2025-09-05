<?php

namespace App\Filament\Resources\PelanggaranRecordResource\Pages;

use App\Filament\Resources\PelanggaranRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPelanggaranRecords extends ListRecords
{
    protected static string $resource = PelanggaranRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
