<?php

namespace App\Filament\Resources\PresensiHarianResource\Pages;

use App\Filament\Resources\PresensiHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresensiHarians extends ListRecords
{
    protected static string $resource = PresensiHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
