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
        return [
            ['Mohon isi data siswa mulai dari baris ke-4 dan jangan mengubah header di baris ke-3.'],
            [],
            // ✅ PERUBAHAN: Menyesuaikan urutan kolom
            [
                'nis',
                'nama_lengkap',
                'nama_kelas',
                'jenis_kelamin',
                'agama',
                'nisn',
                'angkatan',
                'nomor_ortu',
            ]
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        // ✅ PERUBAHAN: Memperbarui baris contoh data sesuai urutan baru
        return [
            [
                'nis' => '19522',
                'nama_lengkap' => 'ADIRA ZAKI DARMAWAN',
                'nama_kelas' => 'XII-J',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'nisn' => '0075488518',
                'angkatan' => '2023',
                'nomor_ortu' => '08123456789 (Opsional)',
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // nis
            'B' => 35, // nama_lengkap
            'C' => 15, // nama_kelas
            'D' => 15, // jenis_kelamin
            'E' => 15, // agama
            'F' => 15, // nisn
            'G' => 15, // angkatan
            'H' => 25, // nomor_ortu
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'H'; // Sesuaikan dengan jumlah kolom
        $headerRange = 'A3:' . $lastColumn . '3';
        $exampleRange = 'A4:' . $lastColumn . '4';
        
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFDDDDDD');

        $sheet->getStyle($exampleRange)->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF808080'));
        
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle($headerRange)->applyFromArray($styleArray);
        $sheet->getStyle($exampleRange)->applyFromArray($styleArray);

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
