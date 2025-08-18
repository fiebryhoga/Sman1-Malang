<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class JurnalGuruExport implements FromView, WithEvents
{
    protected $jurnals;
    protected $namaKelas;
    protected $namaMapel;
    protected $bulan;
    protected $namaGuru;

    public function __construct($jurnals, $namaKelas, $namaMapel, $bulan, $namaGuru)
    {
        $this->jurnals = $jurnals;
        $this->namaKelas = $namaKelas;
        $this->namaMapel = $namaMapel;
        $this->bulan = $bulan;
        $this->namaGuru = $namaGuru;
    }

    public function view(): View
    {
        return view('exports.jurnal-guru', [
            'jurnals' => $this->jurnals,
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
                $lastDataRow = $this->jurnals->count() + 6;

                // Atur lebar kolom
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(12);
                // ✅ PERBAIKAN 1: Perlebar kolom untuk area tanda tangan
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);

                // Buat tebal header informasi dan aktifkan wrap text
                $infoStyle = ['font' => ['bold' => true]];
                $sheet->getStyle('A4:A5')->applyFromArray($infoStyle);
                $sheet->getStyle('E4')->applyFromArray($infoStyle);
                $sheet->getStyle('B4:D4')->getAlignment()->setWrapText(true);
                $sheet->getStyle('F4:H4')->getAlignment()->setWrapText(true);
                $sheet->getStyle('B5:H5')->getAlignment()->setWrapText(true);

                // Style untuk header tabel utama (baris ke-6)
                $headerRange = 'A6:H6';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
                $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Border untuk seluruh tabel
                $fullTableRange = 'A6:H' . $lastDataRow;
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '00000000'],
                        ],
                    ],
                ];
                $sheet->getStyle($fullTableRange)->applyFromArray($styleArray);

                // ✅ PERBAIKAN 2: Atur seluruh isi konten tabel ke tengah secara vertikal
                $dataRange = 'A7:H' . $lastDataRow;
                $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Atur wrap text untuk kolom materi & detail (rata atas tetap bagus untuk teks panjang)
                $sheet->getStyle('C7:D'.$lastDataRow)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

                // Atur perataan tengah horizontal untuk kolom-kolom tertentu
                $sheet->getStyle('A7:B'.$lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E7:H'.$lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Aktifkan wrap text pada sel tanda tangan
                $signatureRow = $lastDataRow + 2;
                $sheet->getStyle('A'.$signatureRow.':H'.$signatureRow)->getAlignment()->setWrapText(true);
            },
        ];
    }
}