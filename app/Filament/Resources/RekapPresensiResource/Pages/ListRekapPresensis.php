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
        if (!$query) {
            return null;
        }

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

        if (empty($kelasId)) {
            return 'Silakan Pilih Kelas Terlebih Dahulu';
        }

        if (empty($mapelId)) {
            return 'Silakan Pilih Mata Pelajaran';
        }

        return 'Tidak Ada Data Ditemukan';
    }

    protected function getEmptyStateDescription(): ?string
    {
        $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
        $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;

        if (empty($kelasId) || empty($mapelId)) {
            return 'Data rekap presensi akan ditampilkan setelah filter utama dipilih.';
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(function () {
                    $filters = $this->tableFilters;
                    $queryString = http_build_query($filters);
                    return route('excel.export', ['filters' => $queryString]);
                })
                ->disabled(function () {
                    $kelasId = $this->tableFilters['kelas_id']['value'] ?? null;
                    $mapelId = $this->tableFilters['mata_pelajaran_id']['value'] ?? null;
                    return empty($kelasId) || empty($mapelId);
                })
                ->openUrlInNewTab(),
        ];
    }
}