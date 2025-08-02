<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use App\Models\Presensi;
use App\Models\KehadiranSiswa;
use App\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AmbilPresensi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.ambil-presensi';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Presensi';
    protected static ?string $title = 'Ambil Presensi Siswa';

    public ?Presensi $presensi = null;
    public array $siswaKehadiran = [];

    // Properti untuk menyimpan ID presensi dari URL
    public $presensi_id;

    /**
     * Mount the page and load initial data.
     */
    public function mount(): void
    {
        // Ambil ID presensi dari parameter URL
        $this->presensi = Presensi::with('kelas.siswas')->find($this->presensi_id);

        if (!$this->presensi) {
            Notification::make()
                ->title('Presensi tidak ditemukan!')
                ->danger()
                ->send();
            $this->redirect(BukaPresensi::getUrl()); // Redirect jika presensi tidak ditemukan
            return;
        }

        // Inisialisasi status kehadiran siswa
        // Default semua siswa hadir
        $this->siswaKehadiran = $this->presensi->kelas->siswas->map(function (Siswa $siswa) {
            return [
                'siswa_id' => $siswa->id,
                'nama_lengkap' => $siswa->nama_lengkap,
                'hadir' => true, // Default hadir
                'status' => 'Hadir',
                'keterangan' => null,
            ];
        })->toArray();
    }

    /**
     * Define the form schema for attendance.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('siswaKehadiran')
                    ->label('Daftar Kehadiran Siswa')
                    ->schema([
                        Hidden::make('siswa_id'),
                        TextInput::make('nama_lengkap')
                            ->label('Nama Siswa')
                            ->disabled(), // Nama siswa tidak bisa diubah
                        Toggle::make('hadir')
                            ->label('Hadir')
                            ->onIcon('heroicon-s-check-circle')
                            ->offIcon('heroicon-s-x-circle')
                            ->onColor('success')
                            ->offColor('danger')
                            ->live() // Perbarui secara real-time saat toggle berubah
                            ->default(true),
                        Select::make('status')
                            ->label('Status Kehadiran')
                            ->options([
                                'Hadir' => 'Hadir',
                                'Izin' => 'Izin',
                                'Sakit' => 'Sakit',
                                'Alpha' => 'Alpha',
                            ])
                            ->default('Hadir')
                            ->hidden(fn (callable $get): bool => $get('hadir')) // Sembunyikan jika 'hadir' true
                            ->required(fn (callable $get): bool => !$get('hadir')), // Wajib jika 'hadir' false
                        TextInput::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Contoh: Demam, Izin keluarga')
                            ->hidden(fn (callable $get): bool => $get('hadir')) // Sembunyikan jika 'hadir' true
                            ->maxLength(255),
                    ])
                    ->columns(3) // Tampilkan 3 kolom per baris
                    ->defaultItems(count($this->siswaKehadiran)) // Jumlah item default
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->disableItemMovement(),
            ])
            ->statePath('siswaKehadiran'); // Pastikan state form terikat ke properti siswaKehadiran
    }

    /**
     * Submit attendance data.
     */
    public function submit(): void
    {
        try {
            DB::beginTransaction();

            foreach ($this->siswaKehadiran as $data) {
                // Tentukan status akhir berdasarkan toggle 'hadir'
                $status = $data['hadir'] ? 'Hadir' : $data['status'];

                KehadiranSiswa::updateOrCreate(
                    [
                        'presensi_id' => $this->presensi->id,
                        'siswa_id' => $data['siswa_id'],
                    ],
                    [
                        'status' => $status,
                        'keterangan' => $data['keterangan'],
                    ]
                );
            }

            DB::commit();

            Notification::make()
                ->title('Presensi berhasil disimpan!')
                ->success()
                ->send();

            // Opsional: Redirect kembali ke halaman Buka Presensi atau halaman lain
            $this->redirect(BukaPresensi::getUrl());

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Terjadi kesalahan saat menyimpan presensi.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Mengatur apakah item navigasi ini harus didaftarkan.
     * Halaman ini tidak perlu muncul di navigasi utama, hanya diakses via redirect.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * Mengatur izin untuk melihat halaman ini.
     */
    public static function canView(): bool
    {
        // Hanya guru yang bisa mengakses halaman ini
        return auth()->user()->hasRole(['Guru Wali Kelas', 'Guru Mata Pelajaran']);
    }
}
