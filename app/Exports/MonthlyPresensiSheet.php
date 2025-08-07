<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Presensi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Carbon;
use App\Models\Siswa;

class MonthlyPresensiSheet implements FromView, WithTitle, WithDrawings
{
    private $filters;
    private $kelasId;
    private $year;
    private $month;

    public function __construct(array $filters, $kelasId, $year, $month)
    {
        $this->filters = $filters;
        $this->kelasId = $kelasId;
        $this->year = $year;
        $this->month = $month;
    }

    public function view(): View
    {
        $startDate = Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $query = Presensi::query()
            ->whereYear('tanggal', $this->year)
            ->whereMonth('tanggal', $this->month)
            ->where('kelas_id', $this->kelasId)
            ->with(['kelas', 'mataPelajaran', 'detailPresensi.siswa']);
            
        if (isset($this->filters['mata_pelajaran_id'])) {
            $query->where('mata_pelajaran_id', $this->filters['mata_pelajaran_id']);
        }
        if (isset($this->filters['guru_id'])) {
            $query->where('guru_id', $this->filters['guru_id']);
        }
        
        $presensis = $query->orderBy('tanggal')->get();
        
        $siswas = Kelas::find($this->kelasId)->siswas->sortBy('nama_lengkap');

        $rekapPerSiswa = $siswas->mapWithKeys(function ($siswa) use ($presensis) {
            $hadir = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;

            foreach ($presensis as $presensi) {
                $detail = $presensi->detailPresensi->firstWhere('siswa_id', $siswa->id);
                if ($detail) {
                    if ($detail->status === 'hadir') $hadir++;
                    if ($detail->status === 'sakit') $sakit++;
                    if ($detail->status === 'izin') $izin++;
                    if ($detail->status === 'alpha') $alpha++;
                }
            }

            return [
                $siswa->id => [
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpha' => $alpha,
                ],
            ];
        });
        
        $data = [];
        $presensis->each(function($presensi) use (&$data) {
            $data[$presensi->tanggal->format('d')] = [
                'tanggal' => $presensi->tanggal->format('Y-m-d'),
                'detail' => $presensi->detailPresensi->keyBy('siswa_id'),
            ];
        });
        
        $daysInMonth = $startDate->daysInMonth;
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dates[] = Carbon::createFromDate($this->year, $this->month, $i)->format('Y-m-d');
        }

        $firstPresensi = $presensis->first();
        
        return view('exports.presensi', [
            'presensis' => $data,
            'siswas' => $siswas,
            'dates' => $dates,
            'rekapPerSiswa' => $rekapPerSiswa,
            'kelas' => Kelas::find($this->kelasId)->nama,
            'mataPelajaran' => $firstPresensi ? $firstPresensi->mataPelajaran->nama : '-',
            'monthName' => $startDate->translatedFormat('F Y'),
        ]);
    }

    public function title(): string
    {
        $kelas = Kelas::find($this->kelasId);
        return $kelas->nama . ' - ' . Carbon::create($this->year, $this->month)->translatedFormat('F Y');
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo Sekolah');
        $drawing->setDescription('Logo Sekolah');
        $drawing->setPath(public_path('img/logo-sekolah.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('B1');

        return [$drawing];
    }
}