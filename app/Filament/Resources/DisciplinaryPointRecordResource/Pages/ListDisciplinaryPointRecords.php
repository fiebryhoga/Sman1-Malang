<?php

namespace App\Filament\Resources\DisciplinaryPointRecordResource\Pages;

use App\Filament\Resources\DisciplinaryPointRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisciplinaryPointRecords extends ListRecords
{
    protected static string $resource = DisciplinaryPointRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
