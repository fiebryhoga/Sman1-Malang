<?php

namespace App\Filament\Resources\RekapPresensiResource\Pages;

use App\Filament\Resources\RekapPresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Model;

class ViewRekapPresensis extends ViewRecord
{
    protected static string $resource = RekapPresensiResource::class;

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
                Section::make('Informasi Sesi')
                    ->schema([
                        TextEntry::make('kelas.nama')->label('Kelas'),
                        TextEntry::make('mataPelajaran.nama')->label('Mata Pelajaran'),
                        TextEntry::make('guru.name')->label('Guru Pengampu'),
                        TextEntry::make('pembuat.name')->label('Dibuat Oleh'),
                        TextEntry::make('tanggal')
                            ->label('Waktu Presensi')
                            ->formatStateUsing(fn(Model $record) => $record->hari . ', ' . $record->tanggal->translatedFormat('d F Y')),
                        TextEntry::make('pertemuan_ke')->label('Pertemuan Ke'),
                        TextEntry::make('materi')->columnSpanFull(),
                    ])->columns(3),
                Section::make('Rekap Kehadiran')
                    ->schema([
                        RepeatableEntry::make('detailPresensi')
                            ->schema([
                                ImageEntry::make('siswa.foto')->label(false)->circular()->defaultImageUrl(fn (Model $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->siswa->nama_lengkap)),
                                Group::make([
                                    TextEntry::make('siswa.nama_lengkap')->label(false)->weight('medium'),
                                    TextEntry::make('updated_at')->label(false)->dateTime('d/m/Y H:i:s')->size('sm')->color('gray'),
                                ])->label('Nama Siswa'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'hadir' => 'success',
                                        'sakit' => 'warning',
                                        'izin' => 'info',
                                        'alpha' => 'danger',
                                    }),
                            ])
                            ->columns(3)->grid(2),
                    ]),
            ]);
    }
}