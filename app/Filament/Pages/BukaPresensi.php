<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use App\Models\Presensi; // Import model Presensi

class BukaPresensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.buka-presensi';
    protected static ?string $navigationGroup = 'Presensi';
    protected static ?string $title = 'Buka Presensi Kelas';

    /**
     * Mengatur tabel untuk menampilkan kelas dan mata pelajaran yang diampu.
     */
    public function table(Table $table): Table
    {
        $userId = auth()->id();
        $user = auth()->user();

        $relevantKelasIds = collect();

        if ($user->hasRole('Guru Wali Kelas')) {
            $relevantKelasIds = $relevantKelasIds->merge(Kelas::where('wali_kelas_id', $userId)->pluck('id'));
        }

        if ($user->hasRole('Guru Mata Pelajaran')) {
            $relevantKelasIds = $relevantKelasIds->merge(DB::table('kelas_mata_pelajaran')
                ->where('user_id', $userId)
                ->pluck('kelas_id'));
        }

        $relevantKelasIds = $relevantKelasIds->unique();

        return $table
            ->query(Kelas::whereIn('id', $relevantKelasIds))
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('waliKelas.name')
                    ->label('Wali Kelas')
                    ->default('Belum diatur'),
                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran Diampu')
                    ->formatStateUsing(function (string $state, Kelas $record) use ($userId, $user) {
                        $subjects = [];

                        if ($user->hasRole('Guru Wali Kelas') && $record->wali_kelas_id === $userId) {
                            $subjects = $record->mataPelajaran->pluck('nama')->toArray();
                        }

                        if ($user->hasRole('Guru Mata Pelajaran')) {
                            $taughtSubjects = $record->mataPelajaran()
                                ->whereHas('teachers', function (Builder $query) use ($userId) {
                                    $query->where('users.id', $userId);
                                })
                                ->pluck('nama')
                                ->toArray();
                            $subjects = array_merge($subjects, $taughtSubjects);
                        }
                        
                        $subjects = array_unique($subjects);
                        return empty($subjects) ? '-' : implode(', ', $subjects);
                    }),
            ])
            ->actions([
                Action::make('bukaPresensi')
                    ->label('Buka Presensi')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->form(function (Kelas $record) use ($userId, $user) {
                        $availableSubjects = collect();

                        if ($user->hasRole('Guru Wali Kelas') && $record->wali_kelas_id === $userId) {
                            $availableSubjects = $availableSubjects->merge($record->mataPelajaran->pluck('nama', 'id'));
                        }

                        if ($user->hasRole('Guru Mata Pelajaran')) {
                            $taughtSubjects = $record->mataPelajaran()
                                ->whereHas('teachers', function (Builder $query) use ($userId) {
                                    $query->where('users.id', $userId);
                                })
                                ->pluck('nama', 'id');
                            $availableSubjects = $availableSubjects->merge($taughtSubjects);
                        }

                        $availableSubjects = $availableSubjects->unique()->toArray();

                        return [
                            Select::make('mata_pelajaran_id')
                                ->label('Pilih Mata Pelajaran')
                                ->options($availableSubjects)
                                ->required()
                                ->placeholder('Pilih mata pelajaran untuk presensi'),
                            DatePicker::make('tanggal_presensi')
                                ->label('Tanggal Presensi')
                                ->default(now())
                                ->required(),
                            TextInput::make('pertemuan_ke')
                                ->label('Pertemuan Ke-')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('materi_pertemuan')
                                ->label('Materi Pertemuan')
                                ->required()
                                ->maxLength(255),
                        ];
                    })
                    ->action(function (array $data, Kelas $record): void {
                        // Simpan data presensi awal ke database
                        $newPresensi = Presensi::create([
                            'kelas_id' => $record->id,
                            'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                            'user_id' => auth()->id(), // Guru yang membuka presensi
                            'tanggal' => $data['tanggal_presensi'],
                            'pertemuan_ke' => $data['pertemuan_ke'],
                            'materi_pertemuan' => $data['materi_pertemuan'],
                        ]);

                        // Arahkan ke halaman ambil presensi
                        $this->redirect(AmbilPresensi::getUrl(['presensi_id' => $newPresensi->id]));
                    }),
            ]);
    }

    /**
     * Mengatur apakah item navigasi ini harus didaftarkan.
     * Hanya tampilkan jika user memiliki peran Guru Wali Kelas atau Guru Mata Pelajaran.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['Guru Wali Kelas', 'Guru Mata Pelajaran']);
    }

    /**
     * Mengatur izin untuk melihat halaman ini.
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['Guru Wali Kelas', 'Guru Mata Pelajaran']);
    }
}
