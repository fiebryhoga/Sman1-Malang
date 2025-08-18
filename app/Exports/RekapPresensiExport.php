<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapPresensiExport implements FromView, WithEvents
{
    protected $siswas;
    protected $presensis;
    protected $rekapData;
    protected $rekapPerSiswa;
    protected $namaKelas;
    protected $namaMapel;
    protected $bulan;
    protected $namaGuru;

    public function __construct($siswas, $presensis, $rekapData, $rekapPerSiswa, $namaKelas, $namaMapel, $bulan, $namaGuru)
    {
        $this->siswas = $siswas;
        $this->presensis = $presensis;
        $this->rekapData = $rekapData;
        $this->rekapPerSiswa = $rekapPerSiswa;
        $this->namaKelas = $namaKelas;
        $this->namaMapel = $namaMapel;
        $this->bulan = $bulan;
        $this->namaGuru = $namaGuru;
    }

    public function view(): View
    {
        return view('exports.rekap-presensi', [
            'siswas' => $this->siswas,
            'presensis' => $this->presensis,
            'rekapData' => $this->rekapData,
            'rekapPerSiswa' => $this->rekapPerSiswa,
            'namaKelas' => $this->namaKelas,
            'namaMapel' => $this->namaMapel,
            'bulan' => $this->bulan,
            'namaGuru' => $this->namaGuru,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $startRow = 8;
                $startColumnIndex = 4; // Kolom 'D'
                
                // Mengatur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(5);
                for ($i = 0; $i < $this->presensis->count(); $i++) {
                    $currentColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumnIndex + $i);
                    $sheet->getColumnDimension($currentColumnLetter)->setWidth(10);
                }
                $lastDateColumnIndex = $startColumnIndex + $this->presensis->count() - 1;
                $keteranganStartColumn = $lastDateColumnIndex + 1;
                $keteranganStartLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($keteranganStartColumn);
                $sheet->getColumnDimension($keteranganStartLetter)->setWidth(5);
                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($keteranganStartColumn + 1))->setWidth(5);
                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($keteranganStartColumn + 2))->setWidth(5);
                $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($keteranganStartColumn + 3))->setWidth(5);

                // Style untuk header tabel
                $sheet->getStyle('A6:Z7')->getFont()->setBold(true);
                $sheet->getStyle('A6:Z7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
                
                // Atur alignment header "Tanggal" dan "Keterangan" ke tengah
                $sheet->getStyle('D6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($keteranganStartLetter.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                
                // Atur header tanggal menjadi vertikal & di tengah
                $dateHeaderRow = 7;
                $sheet->getRowDimension($dateHeaderRow)->setRowHeight(75);
                for ($i = 0; $i < $this->presensis->count(); $i++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumnIndex + $i);
                    $sheet->getStyle($colLetter . $dateHeaderRow)->getAlignment()->setTextRotation(90)->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Atur alignment untuk seluruh kolom keterangan
                $lastStudentRow = $startRow + $this->siswas->count() - 1;
                $keteranganEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($keteranganStartColumn + 3);
                $keteranganRange = $keteranganStartLetter . '7:' . $keteranganEndLetter . $lastStudentRow;
                $sheet->getStyle($keteranganRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Loop untuk mewarnai sel status
                foreach ($this->siswas as $rowIndex => $siswa) {
                    $colIndex = 3; 
                    foreach ($this->presensis as $presensi) {
                        $colIndex++;
                        $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . ($startRow + $rowIndex);
                        $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $status = $this->rekapData[$siswa->id][$presensi->tanggal->format('Y-m-d')] ?? 'A';
                        $color = '';
                        switch ($status) {
                            case 'H': $color = 'D4EDDA'; break;
                            case 'S': $color = 'FFF3CD'; break;
                            case 'I': $color = 'D1ECF1'; break;
                            case 'A': $color = 'F8D7DA'; break;
                        }
                        if ($color) {
                            $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                        }
                    }
                }

                // Hapus styling untuk baris materi dan atur untuk detail pembelajaran
                $detailRowNumber = $startRow + $this->siswas->count();
                $sheet->getStyle('A'.$detailRowNumber.':C'.$detailRowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getRowDimension($detailRowNumber)->setRowHeight(150);
                for ($i = 0; $i < $this->presensis->count(); $i++) {
                    $currentColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startColumnIndex + $i);
                    $sheet->getStyle($currentColumnLetter . $detailRowNumber)->getAlignment()
                        ->setTextRotation(90)
                        ->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setIndent(1);
                }
                
                // ✅ PERUBAHAN: Menambahkan border hitam ke seluruh tabel utama
                $lastColumnLetter = $keteranganEndLetter;
                $lastRowNumber = $detailRowNumber;
                $fullTableRange = 'A6:' . $lastColumnLetter . $lastRowNumber;
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ],
                ];
                $sheet->getStyle($fullTableRange)->applyFromArray($styleArray);

            },
        ];
    }
}