<?php

namespace App\Filament\Resources\DisciplinaryPointCategoryResource\Pages;

use App\Filament\Resources\DisciplinaryPointCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDisciplinaryPointCategory extends EditRecord
{
    protected static string $resource = DisciplinaryPointCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
