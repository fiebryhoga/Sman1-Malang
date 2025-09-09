<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiEkstrakurikulerResource\Pages;
use App\Models\PresensiEkstrakurikuler;
use App\Models\Siswa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PresensiEkstrakurikulerResource extends Resource
{
    protected static ?string $model = PresensiEkstrakurikuler::class;
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Buat Sesi Presensi Baru')
                    ->schema([
                        TextInput::make('kegiatan')->required(),
                        DatePicker::make('tanggal')->default(now())->required(),
                        Textarea::make('catatan')->label('Catatan (Opsional)')->columnSpanFull(),
                    ])->columns(2)->visibleOn('create'),
                
                Section::make('Detail Sesi')
                    ->schema([
                        Placeholder::make('kegiatan')->content(fn($record)=>$record->kegiatan),
                        Placeholder::make('tanggal')->content(fn($record)=>$record->tanggal->translatedFormat('l, d F Y')),
                    ])->columns(2)->visibleOn('edit'),
                
                Section::make('Kelola Kehadiran Anggota')
                    ->schema([
                        Repeater::make('details')
                            ->relationship('details', function (Builder $query) {
                                return $query->with('siswa.kelas');
                            })
                            ->schema([
                                Grid::make(['md' => 3])
                                    ->schema([
                                        Placeholder::make('info_siswa')
                                            ->label(false)
                                            ->columnSpan(2)
                                            ->content(function (?Model $record): HtmlString {
                                                if (!$record || !$record->siswa) {
                                                    return new HtmlString("<div>Data siswa tidak ditemukan.</div>");
                                                }
                                                $siswa = $record->siswa;
                                                $fotoPath = $siswa->foto;
                                                $nama = $siswa->nama_lengkap;
                                                $nis = $siswa->nis;
                                                $kelas = $siswa->kelas->nama ?? 'Tanpa Kelas';
                                                
                                                $fotoUrl = $fotoPath ? Storage::url($fotoPath) : 'https://ui-avatars.com/api/?name=' . urlencode($nama);

                                                // ✅ PERBAIKAN: Mengganti style="color:..." dengan class CSS
                                                return new HtmlString(
                                                    "<div style='display: flex; align-items: center;'>" .
                                                    "<img src='{$fotoUrl}' style='width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; margin-right: 12px;' />" .
                                                    "<div>" .
                                                    "<div class='font-medium text-gray-950 dark:text-white'>" . e($nama) . "</div>" .
                                                    "<div class='text-sm text-gray-500 dark:text-gray-400'>" . e($nis) . " | " . e($kelas) . "</div>" .
                                                    "</div>" .
                                                    "</div>"
                                                );
                                            }),
                                        Select::make('status')
                                            ->label(false)
                                            ->options(['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'])
                                            ->required(),
                                    ]),
                            ])
                            ->addable(false)->deletable(false)->reorderable(false),
                    ])->visibleOn('edit'),
            ]);
    }
    
    public static function table(Table $table): Table { return $table->columns([]); }
    
    public static function getPages(): array
    {
        return [
            'create' => Pages\CreatePresensiEkstrakurikuler::route('/create'),
            'edit' => Pages\EditPresensiEkstrakurikuler::route('/{record}/edit'),
        ];
    }
}