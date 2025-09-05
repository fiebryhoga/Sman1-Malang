<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles, WithTitle
{
    /**
     * @return array
     */
    public function headings(): array
    {
        // ✅ PERUBAHAN: Menambahkan baris instruksi di atas header
        return [
            // Baris 1: Instruksi
            ['Mohon isi data siswa mulai dari baris ke-4 dan jangan mengubah header di baris ke-3.'],
            // Baris 2: Spasi
            [],
            // Baris 3: Header Tabel
            [
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'nama_kelas',
                'nomor_ortu',
            ]
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // ✅ PERUBAHAN: Menambahkan baris contoh data
        return [
            [
                'nis' => 'Contoh: 12345',
                'nama_lengkap' => 'Contoh: Budi Santoso',
                'jenis_kelamin' => 'Isi L atau P (Opsional)',
                'nama_kelas' => 'Contoh: X-TKJ-A (Opsional)',
                'nomor_ortu' => 'Contoh: 08123456789 (Opsional)',
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 35,
            'C' => 25,
            'D' => 25,
            'E' => 30,
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Gabungkan sel untuk baris instruksi
        $sheet->mergeCells('A1:E1');
        
        // Style untuk baris instruksi (baris 1)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080')); // Abu-abu gelap
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Style untuk header tabel (baris 3)
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFDDDDDD'); // Abu-abu terang

        // Style untuk baris contoh data (baris 4)
        $sheet->getStyle('A4:E4')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
        
        // ✅ PERUBAHAN: Tambahkan border hitam pada header dan baris contoh
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:E4')->applyFromArray($styleArray);

        return [];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Template Data Siswa';
    }
}