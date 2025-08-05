<?php

namespace App\Filament\Resources\DisciplinaryPointCategoryResource\Pages;

use App\Filament\Resources\DisciplinaryPointCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisciplinaryPointCategories extends ListRecords
{
    protected static string $resource = DisciplinaryPointCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
