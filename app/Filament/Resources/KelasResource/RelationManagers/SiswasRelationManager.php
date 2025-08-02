<?php

namespace App\Filament\Resources\KelasResource\RelationManagers;

use App\Models\Siswa;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model; // Import Model

class SiswasRelationManager extends RelationManager
{
    protected static string $relationship = 'siswas';

    /**
     * Mengatur apakah aksi 'create' (misalnya, 'masukkanSiswa') dapat dilakukan.
     * Hanya izinkan jika pengguna memiliki izin 'mengelola_siswa'.
     */
    public function canCreate(): bool
    {
        return auth()->user()->can('mengelola_siswa');
    }

    /**
     * Mengatur apakah aksi 'edit' (tidak ada aksi edit langsung di sini, tapi baik untuk didefinisikan).
     * Menggunakan Illuminate\Database\Eloquent\Model untuk kompatibilitas.
     */
    public function canEdit(Model $record): bool // Diubah dari Siswa $record
    {
        return auth()->user()->can('mengelola_siswa');
    }

    /**
     * Mengatur apakah aksi 'delete' (misalnya, 'keluarkan') dapat dilakukan.
     * Hanya izinkan jika pengguna memiliki izin 'mengelola_siswa'.
     * Menggunakan Illuminate\Database\Eloquent\Model untuk kompatibilitas.
     */
    public function canDelete(Model $record): bool // Diubah dari Siswa $record
    {
        return auth()->user()->can('mengelola_siswa');
    }

    /**
     * Mengatur apakah aksi 'delete massal' dapat dilakukan.
     * Hanya izinkan jika pengguna memiliki izin 'mengelola_siswa'.
     */
    public function canDeleteAny(): bool
    {
        return auth()->user()->can('mengelola_siswa');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]); // Form tidak digunakan untuk membuat/mengedit siswa langsung di sini
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('nis')->label('NIS'),
                Tables\Columns\TextColumn::make('nama_lengkap'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('masukkanSiswa')
                    ->label('Masukkan / Pindahkan Siswa')
                    ->form([
                        Select::make('siswa_ids')
                            ->label('Pilih Siswa')
                            ->multiple() // Mengizinkan pemilihan ganda
                            ->options(function () {
                                $ownerId = $this->getOwnerRecord()->id;
                                return Siswa::with('kelas') // Eager load relasi kelas
                                    ->where('kelas_id', '!=', $ownerId)
                                    ->orWhereNull('kelas_id')
                                    ->get()
                                    ->mapWithKeys(function ($siswa) {
                                        $label = $siswa->nama_lengkap;
                                        // Tambahkan keterangan jika siswa sudah punya kelas
                                        if ($siswa->kelas_id) {
                                            $label .= " (Kelas: {$siswa->kelas->nama})";
                                        }
                                        return [$siswa->id => $label];
                                    });
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        // Update beberapa siswa sekaligus
                        Siswa::whereIn('id', $data['siswa_ids'])
                             ->update(['kelas_id' => $this->getOwnerRecord()->id]);
                    })
                    ->successNotificationTitle('Siswa berhasil dimasukkan/dipindahkan')
                    // Aksi ini hanya terlihat jika pengguna memiliki izin 'mengelola_siswa'
                    ->visible(fn (): bool => auth()->user()->can('mengelola_siswa')),
            ])
            ->actions([
                Tables\Actions\Action::make('keluarkan')
                    ->label('Keluarkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Siswa $record) => $record->update(['kelas_id' => null]))
                    // Aksi ini hanya terlihat jika pengguna memiliki izin 'mengelola_siswa'
                    ->visible(fn (): bool => auth()->user()->can('mengelola_siswa')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('keluarkan_massal')
                        ->label('Keluarkan Siswa Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['kelas_id' => null]))
                        // Aksi ini hanya terlihat jika pengguna memiliki izin 'mengelola_siswa'
                        ->visible(fn (): bool => auth()->user()->can('mengelola_siswa')),
                ])->visible(fn (): bool => auth()->user()->can('mengelola_siswa')), // Grup aksi massal juga disembunyikan
            ]);
    }
}
