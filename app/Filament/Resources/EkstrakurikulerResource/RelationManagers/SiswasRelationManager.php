<?php

namespace App\Filament\Resources\EkstrakurikulerResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction; // ✅ 1. Impor class untuk tombol Attach
use Filament\Tables\Actions\DetachAction; // ✅ 2. Impor class untuk tombol Detach
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiswaRelationManager extends RelationManager
{
    protected static string $relationship = 'siswas';
    
    protected static ?string $title = 'Anggota Ekstrakurikuler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini tidak kita gunakan, jadi biarkan kosong
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_lengkap')
            ->columns([
                TextColumn::make('nis'),
                TextColumn::make('nama_lengkap'),
                TextColumn::make('kelas.nama'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ✅ 3. Tambahkan tombol "Attach" di sini
                AttachAction::make()
                    ->preloadRecordSelect() // Membuat dropdown pencarian lebih cepat
                    ->multiple(), // Izinkan memilih banyak siswa sekaligus
            ])
            ->actions([
                // ✅ 4. Tambahkan tombol "Detach" di setiap baris
                DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(), // Izinkan hapus banyak siswa sekaligus
                ]),
            ]);
    }
}