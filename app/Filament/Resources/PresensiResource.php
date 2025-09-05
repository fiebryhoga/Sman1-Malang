<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Models\JadwalMengajar;
use App\Models\Presensi;
use App\Models\Siswa;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $label = 'Presensi Mata Pelajaran';
        protected static ?string $pluralLabel = 'Presensi Mata Pelajaran';


    public static function canViewAny(): bool
    {
        return auth()->user()->can('melihat_presensi_semua') || auth()->user()->can('melihat_presensi_diampu');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_presensi_diampu');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('mengelola_presensi_semua') ||
               (auth()->user()->can('mengelola_presensi_diampu') && $record->guru_id === auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informasi Presensi')
                        ->schema([
                            Section::make('Detail Sesi Presensi')
                                ->schema([
                                    Placeholder::make('jadwal_info')
                                        ->label('Kelas & Mata Pelajaran')
                                        ->content(function (?Presensi $record): string {
                                            if (!$record) return '-';
                                            return "{$record->kelas->nama} - {$record->mataPelajaran->nama}";
                                        })
                                        ->visibleOn('edit'),
                                    Select::make('jadwal_id')
                                        ->label('Pilih Kelas & Mata Pelajaran')
                                        ->options(function () {
                                            $user = auth()->user();
                                            $query = JadwalMengajar::query()->with(['kelas', 'mataPelajaran', 'jamPelajaran']);

                                            if (!$user->can('mengelola_presensi_semua')) {
                                                $query->where('user_id', $user->id);
                                            }

                                            $jadwal = $query->get();

                                            return $jadwal->mapWithKeys(function ($item) {
                                                $jam = $item->jamPelajaran->pluck('jam_ke')->join(', ');
                                                return [
                                                    $item->id => "{$item->mataPelajaran->nama} - {$item->kelas->nama} - Jam ke-{$jam}"
                                                ];
                                            });
                                        })
                                        ->live()
                                        ->required()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            if (blank($state)) return;
                                            $jadwal = JadwalMengajar::find($state);
                                            if (!$jadwal) return;
                                            $siswas = $jadwal->kelas->siswas;
                                            $detailData = $siswas->map(fn (Siswa $siswa) => [
                                                'siswa_id' => $siswa->id,
                                                'nama_siswa' => $siswa->nama_lengkap,
                                                'foto_siswa' => $siswa->foto,
                                                'is_hadir' => true,
                                            ])->all();
                                            $set('detailPresensi', $detailData);
                                        })
                                        ->hiddenOn('edit'),

                                    DatePicker::make('tanggal')->default(now())->required(),
                                    TextInput::make('pertemuan_ke')->numeric()->required()->label('Pertemuan Ke'),
                                    Textarea::make('materi')->required()->columnSpanFull(),
                                    
                                    // ✅ TAMBAHKAN INI (FORM)
                                    Textarea::make('detail_pembelajaran')
                                        ->label('Detail Pembelajaran')
                                        ->nullable() // Opsional, bisa dihapus jika wajib diisi
                                        ->columnSpanFull(),

                                ])->columns(2),
                        ]),
                    Wizard\Step::make('Absensi Siswa')
                        // ... (schema absensi siswa tidak berubah)
                        ->schema([
                            Section::make('Daftar Kehadiran Siswa')
                                ->schema([
                                    Repeater::make('detailPresensi')
                                        ->relationship()
                                        ->schema([
                                            Grid::make(5)->schema([
                                                Hidden::make('siswa_id'),
                                                Placeholder::make('info_siswa')->label(false)->columnSpan(3)
                                                    ->content(function (Get $get): HtmlString {
                                                        $nama = $get('nama_siswa');
                                                        $fotoPath = $get('foto_siswa');
                                                        $namaDefault = $nama ?: 'Memuat...';
                                                        $fotoUrl = $fotoPath ? Storage::url($fotoPath) : 'https://ui-avatars.com/api/?name=' . urlencode($namaDefault);
                                                        return new HtmlString(
                                                            '<div class="flex items-center gap-x-4">
                                                                <img src="' . $fotoUrl . '" alt="Foto ' . e($namaDefault) . '" class="w-10 h-10 rounded-full object-cover" />
                                                                <span class="font-medium text-gray-950 dark:text-white">' . e($namaDefault) . '</span>
                                                            </div>'
                                                        );
                                                    }),
                                                Toggle::make('is_hadir')->label(false)->columnSpan(1)->default(true)->live(),
                                                Select::make('status')->label(false)->columnSpan(1)
                                                    ->options(['sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'])
                                                    ->default('alpha')->required()->hidden(fn (Get $get) => $get('is_hadir')),
                                            ])->columnSpanFull(),
                                        ])
                                        ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => self::prepareDetailPresensiData($data))
                                        ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => self::prepareDetailPresensiData($data))
                                        ->afterStateHydrated(function (array $state, Set $set): void {
                                            $siswaIds = array_column($state, 'siswa_id');
                                            $siswas = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');
                                            $newState = [];
                                            foreach ($state as $key => $item) {
                                                $siswa = $siswas->get($item['siswa_id']);
                                                if ($siswa) {
                                                    $item['nama_siswa'] = $siswa->nama_lengkap;
                                                    $item['foto_siswa'] = $siswa->foto;
                                                    $item['is_hadir'] = $item['status'] === 'hadir';
                                                }
                                                $newState[$key] = $item;
                                            }
                                            $set('detailPresensi', $newState);
                                        })
                                        ->grid(1)->addable(false)->deletable(false)->reorderable(false),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Sesi')
                    ->schema([
                        Infolists\Components\TextEntry::make('kelas.nama'),
                        Infolists\Components\TextEntry::make('mataPelajaran.nama'),
                        Infolists\Components\TextEntry::make('guru.name')->label('Guru Pengampu'),
                        Infolists\Components\TextEntry::make('pembuat.name')->label('Dibuat Oleh'),
                        Infolists\Components\TextEntry::make('tanggal')
                            ->label('Waktu Presensi')
                            ->formatStateUsing(fn(Model $record) => $record->hari . ', ' . $record->tanggal->translatedFormat('d F Y')),
                        Infolists\Components\TextEntry::make('jam_pelajaran_ke')
                            ->label('Jam Pelajaran Ke')
                            ->getStateUsing(fn (Model $record) => $record->jadwalMengajar?->jamPelajaran?->pluck('jam_ke')?->join(', ') ?? '-'),
                        Infolists\Components\TextEntry::make('pertemuan_ke'),
                        Infolists\Components\TextEntry::make('materi')->columnSpanFull(),
                        
                        // ✅ TAMBAHKAN INI (INfolist)
                        Infolists\Components\TextEntry::make('detail_pembelajaran')
                            ->label('Detail Pembelajaran')
                            ->columnSpanFull()
                            ->markdown(), // Menggunakan markdown untuk tampilan yang lebih baik

                    ])->columns(3),
                Infolists\Components\Section::make('Rekap Kehadiran')
                    // ... (schema rekap kehadiran tidak berubah)
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('detailPresensi')
                            ->schema([
                                Infolists\Components\ImageEntry::make('siswa.foto')->label(false)->circular()->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->siswa->nama_lengkap)),
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('siswa.nama_lengkap')->label(false)->weight('medium'),
                                    Infolists\Components\TextEntry::make('updated_at')->label(false)->dateTime('d/m/Y H:i:s')->size('sm')->color('gray'),
                                ])->label('Nama Siswa'),
                                Infolists\Components\TextEntry::make('status')
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
    
    // ... (sisa file tidak perlu diubah)
    protected static function prepareDetailPresensiData(array $data): array
    {
        $data['status'] = $data['is_hadir'] ? 'hadir' : ($data['status'] ?? 'alpha');
        unset($data['is_hadir'], $data['nama_siswa'], $data['foto_siswa'], $data['info_siswa']);
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kelas.nama')->searchable(),
                TextColumn::make('mataPelajaran.nama')->searchable(),
                TextColumn::make('tanggal')
                    ->label('Waktu Presensi')
                    ->formatStateUsing(function (Model $record) {
                        return $record->hari . ', ' . $record->tanggal->translatedFormat('d F Y');
                    })
                    ->sortable(),
                TextColumn::make('pertemuan_ke'),
                TextColumn::make('pembuat.name')->label('Dibuat Oleh')->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->can('melihat_presensi_semua')) {
                    $query->where('guru_id', auth()->id());
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'view' => Pages\ViewPresensi::route('/{record}'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}