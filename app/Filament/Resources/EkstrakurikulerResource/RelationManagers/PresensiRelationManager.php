<?php
namespace App\Filament\Resources\EkstrakurikulerResource\RelationManagers;

use App\Filament\Resources\PresensiEkstrakurikulerResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PresensiRelationManager extends RelationManager
{
    protected static string $relationship = 'presensi';
    protected static ?string $title = 'Sesi Presensi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tanggal')
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->locale('id_ID')->translatedFormat('l, d F Y'))
                    ->sortable(),
                
                // ✅ PERBAIKAN: Membatasi panjang teks yang ditampilkan
                TextColumn::make('kegiatan')
                    ->limit(20) // Batasi teks hingga 30 karakter
                    ->tooltip(fn ($state) => $state), // Tampilkan isi lengkap saat di-hover

                TextColumn::make('catatan')
                    ->limit(30) // Batasi teks hingga 30 karakter
                    ->tooltip(fn ($state) => $state), // Tampilkan isi lengkap saat di-hover

                TextColumn::make('pencatat.name'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buka Sesi Presensi Baru') // Mengubah label agar lebih jelas
                    ->url(fn (): string => PresensiEkstrakurikulerResource::getUrl('create', ['ekstrakurikuler_id' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                EditAction::make()
                    ->label('Kelola Presensi')
                    ->url(fn ($record): string => PresensiEkstrakurikulerResource::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}