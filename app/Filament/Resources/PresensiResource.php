<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Models\Kelas;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Akademik';

    public static function canViewAny(): bool
    {
        // --- PERBAIKAN FINAL ---
        // Cek secara eksplisit apakah user memiliki salah satu peran yang diizinkan.
        // Ini adalah cara yang lebih andal jika cache permission bermasalah.
        if (auth()->user()->hasRole(['Super Admin', 'Guru Wali Kelas', 'Guru Mata Pelajaran'])) {
            return true;
        }

        // Sebagai fallback, tetap periksa permission untuk peran lain di masa depan.
        return auth()->user()->can('melihat_presensi');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('mengelola_presensi_diampu') || auth()->user()->can('mengelola_presensi_semua');
    }

    public static function canEdit(Model $record): bool
    {
        // Izinkan edit jika punya izin 'semua', ATAU jika punya izin 'diampu' DAN presensi ini dibuat olehnya.
        return auth()->user()->can('mengelola_presensi_semua') ||
               (auth()->user()->can('mengelola_presensi_diampu') && $record->guru_id === auth()->id());
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
                        Infolists\Components\TextEntry::make('created_at')->label('Waktu Presensi')->dateTime('d F Y, H:i:s'),
                        Infolists\Components\TextEntry::make('pertemuan_ke'),
                        Infolists\Components\TextEntry::make('materi')->columnSpanFull(),
                    ])->columns(3),
                Infolists\Components\Section::make('Rekap Kehadiran')
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informasi Presensi')
                        ->schema([
                            Section::make('Detail Sesi Presensi')
                                ->schema([
                                    Select::make('jadwal_id')
                                        ->label('Pilih Kelas & Mata Pelajaran')
                                        ->options(function () {
                                            $user = auth()->user();
                                            $query = DB::table('kelas_mata_pelajaran')
                                                ->join('kelas', 'kelas_mata_pelajaran.kelas_id', '=', 'kelas.id')
                                                ->join('mata_pelajarans', 'kelas_mata_pelajaran.mata_pelajaran_id', '=', 'mata_pelajarans.id');

                                            // Jika user tidak punya izin 'semua', filter berdasarkan kelas yang diajar
                                            if (!$user->can('mengelola_presensi_semua')) {
                                                $query->where('kelas_mata_pelajaran.user_id', $user->id);
                                            }

                                            $jadwal = $query->select('kelas_mata_pelajaran.id', 'kelas.nama as nama_kelas', 'mata_pelajarans.nama as nama_mapel')->get();
                                            
                                            return $jadwal->pluck('nama_mapel', 'id')->mapWithKeys(function ($mapel, $id) use ($jadwal) {
                                                $item = $jadwal->firstWhere('id', $id);
                                                return [$id => "{$item->nama_kelas} - {$mapel}"];
                                            });
                                        })
                                        ->live()
                                        ->required()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            if (blank($state)) return;
                                            $jadwal = DB::table('kelas_mata_pelajaran')->find($state);
                                            if (!$jadwal) return;
                                            $siswas = Kelas::find($jadwal->kelas_id)->siswas;
                                            $detailData = $siswas->map(fn (Siswa $siswa) => [
                                                'siswa_id' => $siswa->id,
                                                'nama_siswa' => $siswa->nama_lengkap,
                                                'foto_siswa' => $siswa->foto,
                                                'is_hadir' => true,
                                            ])->all();
                                            $set('detailPresensi', $detailData);
                                        })
                                        ->disabled(fn (string $operation): bool => $operation !== 'create'),
                                    DatePicker::make('tanggal')->default(now())->required(),
                                    TextInput::make('pertemuan_ke')->numeric()->required()->label('Pertemuan Ke'),
                                    Textarea::make('materi')->required(),
                                ])->columns(2),
                        ]),
                    Wizard\Step::make('Absensi Siswa')
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
                                                        $fotoUrl = $fotoPath ? Storage::url($fotoPath) : 'https://ui-avatars.com/api/?name=' . urlencode($nama);
                                                        return new HtmlString(
                                                            '<div class="flex items-center gap-x-4">
                                                                <img src="' . $fotoUrl . '" alt="Foto ' . e($nama) . '" class="w-10 h-10 rounded-full object-cover" />
                                                                <span class="font-medium text-gray-950 dark:text-white">' . e($nama) . '</span>
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
                                        ->afterStateHydrated(function (Repeater $component, ?Model $record) {
                                            if (!$record) return;
                                            $details = $record->detailPresensi->mapWithKeys(function ($detail) {
                                                return [$detail->id => [
                                                    'siswa_id' => $detail->siswa_id,
                                                    'nama_siswa' => $detail->siswa->nama_lengkap,
                                                    'foto_siswa' => $detail->siswa->foto,
                                                    'is_hadir' => $detail->status === 'hadir',
                                                    'status' => $detail->status === 'hadir' ? 'alpha' : $detail->status,
                                                ]];
                                            })->all();
                                            $component->state($details);
                                        })
                                        ->grid(1)->addable(false)->deletable(false)->reorderable(false),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

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
                TextColumn::make('kelas.nama'),
                TextColumn::make('mataPelajaran.nama'),
                TextColumn::make('tanggal')->date('d F Y', 'Asia/Jakarta'),
                TextColumn::make('pertemuan_ke'),
                TextColumn::make('pembuat.name')->label('Dibuat Oleh'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
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
