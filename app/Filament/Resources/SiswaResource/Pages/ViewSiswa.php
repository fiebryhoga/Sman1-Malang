<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry; // <-- Tambahkan ini
use App\Models\Siswa;

class ViewSiswa extends ViewRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Poin Kedisiplinan')
                    ->schema([
                        TextEntry::make('disciplinary_points')
                            ->label('Poin Kedisiplinan Saat Ini')
                            ->getStateUsing(fn (Siswa $record) => "{$record->disciplinary_points}/200")
                            ->badge()
                            ->color(fn (Siswa $record) => $record->disciplinary_points > 200 ? 'danger' : 'success'),
                    ]),
                
                Section::make('Data Pribadi Siswa')
                    ->schema([
                        // --- PENAMBAHAN FOTO DI SINI ---
                        ImageEntry::make('foto')
                            ->label('Foto Siswa')
                            ->circular(),
                        // --- AKHIR PENAMBAHAN ---
                        TextEntry::make('nis')->label('Nomor Induk Siswa (NIS)'),
                        TextEntry::make('nama_lengkap'),
                        TextEntry::make('jenis_kelamin'),
                        TextEntry::make('kelas.nama')->label('Kelas'),
                        TextEntry::make('nomor_ortu')->label('Nomor HP Orang Tua'),
                    ])
            ]);
    }
}