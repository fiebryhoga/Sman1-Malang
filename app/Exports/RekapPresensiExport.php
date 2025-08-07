<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\MonthlyPresensiSheet;

class RekapPresensiExport implements WithMultipleSheets
{
    use Exportable;

    private $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        $query = Presensi::query();
        
        // Terapkan filter yang ada
        if (isset($this->filters['kelas_id'])) {
            $query->where('kelas_id', $this->filters['kelas_id']);
        }
        if (isset($this->filters['mata_pelajaran_id'])) {
            $query->where('mata_pelajaran_id', $this->filters['mata_pelajaran_id']);
        }
        if (isset($this->filters['guru_id'])) {
            $query->where('guru_id', $this->filters['guru_id']);
        }
        if (isset($this->filters['tahun'])) {
            $query->whereYear('tanggal', $this->filters['tahun']);
        }
        if (isset($this->filters['bulan'])) {
            $query->whereMonth('tanggal', $this->filters['bulan']);
        }
        if (isset($this->filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $this->filters['tanggal_mulai']);
        }
        if (isset($this->filters['tanggal_selesai'])) {
            $query->whereDate('tanggal', '<=', $this->filters['tanggal_selesai']);
        }

        // Ambil semua kombinasi kelas, tahun, dan bulan yang unik
        $combinations = $query->selectRaw('DISTINCT kelas_id, YEAR(tanggal) as year, MONTH(tanggal) as month')
                              ->orderBy('kelas_id')
                              ->orderBy('year')
                              ->orderBy('month')
                              ->get();

        foreach ($combinations as $combination) {
            $sheets[] = new MonthlyPresensiSheet(
                $this->filters,
                $combination->kelas_id,
                $combination->year,
                $combination->month
            );
        }

        return $sheets;
    }
}