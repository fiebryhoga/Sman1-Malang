<?php

namespace App\Filament\Resources\MataPelajaranResource\Pages;

use App\Filament\Resources\MataPelajaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Model;

class ViewMataPelajaran extends ViewRecord
{
    protected static string $resource = MataPelajaranResource::class;

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
                Section::make('Informasi Mata Pelajaran')
                    ->schema([
                        TextEntry::make('nama')->label('Nama Mata Pelajaran'),
                    ]),
                Section::make('Jadwal Mengajar')
                    ->schema([
                        RepeatableEntry::make('jadwalMengajar')
                            ->label(false)
                            ->schema([
                                TextEntry::make('kelas.nama')->label('Kelas'),
                                TextEntry::make('guru.name')->label('Guru Pengampu'),
                                TextEntry::make('jamPelajaran.jam_ke')->label('Jam Pelajaran Ke')
                                    ->formatStateUsing(fn (string $state) => "Jam ke-{$state}"),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}