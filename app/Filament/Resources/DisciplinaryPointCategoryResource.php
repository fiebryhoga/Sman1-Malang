<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisciplinaryPointCategoryResource\Pages;
use App\Models\DisciplinaryPointCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DisciplinaryPointCategoryResource extends Resource
{
    protected static ?string $model = DisciplinaryPointCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $label = 'Kategori Poin';
    protected static ?string $navigationGroup = 'Kedisiplinan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Pelanggaran')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'), 
                TextInput::make('points')
                    ->label('Jumlah Poin')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nama Pelanggaran'),
                TextColumn::make('points')->searchable()->sortable()->label('Poin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisciplinaryPointCategories::route('/'),
            'create' => Pages\CreateDisciplinaryPointCategory::route('/create'),
            'edit' => Pages\EditDisciplinaryPointCategory::route('/{record}/edit'),
        ];
    }
}
