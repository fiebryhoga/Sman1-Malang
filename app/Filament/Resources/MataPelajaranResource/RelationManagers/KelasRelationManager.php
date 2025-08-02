<?php

namespace App\Filament\Resources\MataPelajaranResource\RelationManagers;

use App\Models\Kelas;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KelasRelationManager extends RelationManager
{
    protected static string $relationship = 'kelas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Guru Pengampu')
                    ->options(User::all()->pluck('name', 'id')) // Semua user bisa jadi guru
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('guru')
                    ->label('Guru Pengampu')
                    ->getStateUsing(function ($record) {
                        // Ambil nama guru dari pivot table
                        return User::find($record->pivot->user_id)?->name ?? 'N/A';
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        // PERBAIKAN: Gunakan ->options() untuk memuat semua kelas secara langsung
                        $action->getRecordSelect()
                            ->label('Pilih Kelas')
                            ->options(Kelas::all()->pluck('nama', 'id'))
                            ->searchable(),
                        Select::make('user_id')
                            ->label('Guru Pengampu')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ganti Guru'),
                Tables\Actions\DetachAction::make(),
            ]);
    }
}
