<?php

namespace App\Filament\Resources\PresensiHarianResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    
    protected static ?string $title = 'Kehadiran Siswa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini tidak kita gunakan untuk edit, jadi biarkan saja
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('siswa.nama_lengkap')
            ->columns([
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                
                SelectColumn::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpha' => 'Alpha',
                    ])
                    ->sortable(),
                
                TextColumn::make('keterangan_waktu')
                    ->label('Keterangan')
                    // ✅ PERUBAHAN DI SINI
                    ->color(fn (?string $state): string => match (true) {
                        is_null($state) => 'gray',
                        Str::contains($state, 'Terlambat') => 'danger',
                        Str::contains($state, 'Lebih Awal') => 'success',
                        default => 'primary',
                    }),
                    // ->weight('bold'),
                
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->paginated([10, 25, 50, 100, 'all']);
    }
}