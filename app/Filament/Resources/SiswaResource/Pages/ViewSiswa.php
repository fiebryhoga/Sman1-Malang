<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

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
                Section::make('Data Pribadi Siswa')
                    ->schema([
                        ImageEntry::make('foto')->circular()->columnSpanFull(),
                        TextEntry::make('nis')->label('NIS'),
                        TextEntry::make('nisn')->label('NISN'),
                        TextEntry::make('nama_lengkap'),
                        TextEntry::make('jenis_kelamin'),
                        TextEntry::make('kelas.nama')->label('Kelas'),
                        TextEntry::make('agama'),
                        TextEntry::make('angkatan'),
                        TextEntry::make('nomor_ortu')->label('Nomor HP Orang Tua'),
                    ])->columns(2)
            ]);
    }
}