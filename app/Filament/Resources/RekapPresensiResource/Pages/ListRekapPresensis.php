<?php

namespace App\Filament\Resources\RekapPresensiResource\Pages;

use App\Filament\Resources\RekapPresensiResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRekapPresensis extends ListRecords
{
    protected static string $resource = RekapPresensiResource::class;

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();
        if (!$query) { return null; }

        $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
        $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;

        if (empty($kelasId) || empty($mapelId)) {
            return $query->whereRaw('0 = 1');
        }
        return $query;
    }

    protected function getEmptyStateHeading(): ?string
    {
        $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
        $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;
        if (empty($kelasId) || empty($mapelId)) {
            return 'Silakan Pilih Kelas dan Mata Pelajaran';
        }
        return 'Tidak ada data rekap presensi ditemukan';
    }
    
    protected function getEmptyStateDescription(): ?string
    {
        $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
        $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;
        if (empty($kelasId) || empty($mapelId)) {
            return 'Data akan ditampilkan setelah Anda memilih filter yang diperlukan.';
        }
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Rekap Siswa')
                ->icon('heroicon-o-users')
                ->color('primary')
                ->url(fn (): string => route('presensi.export_rekap', ['filters' => $this->tableFilters]))
                ->disabled(function () {
                    $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
                    $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;
                    return empty($kelasId) || empty($mapelId);
                })
                ->openUrlInNewTab(),

            Action::make('download_jurnal')
                ->label('Download Jurnal Guru')
                ->icon('heroicon-o-book-open')
                ->color('success')
                ->url(fn (): string => route('presensi.export_jurnal', ['filters' => $this->tableFilters]))
                ->disabled(function () {
                    $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
                    $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;
                    return empty($kelasId) || empty($mapelId);
                })
                ->openUrlInNewTab(),
            
        ];
    }
}