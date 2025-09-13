<?php

    namespace App\Exports;

    use Maatwebsite\Excel\Concerns\FromArray;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use Maatwebsite\Excel\Concerns\WithColumnWidths;
    use Maatwebsite\Excel\Concerns\WithTitle;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    class GuruTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles, WithTitle
    {
        public function headings(): array
        {
            return [
                ['Mohon isi data guru mulai dari baris ke-4 dan jangan mengubah header di baris ke-3.'],
                [],
                // Header kolom yang disederhanakan
                [
                    'nip',
                    'nama_lengkap',
                    'password',
                    'no_telepon',
                    'email',
                ]
            ];
        }

        public function array(): array
        {
            // Contoh data yang disederhanakan
            return [
                [
                    'nip' => 'Contoh: 198501012010011001',
                    'nama_lengkap' => 'Contoh: Budi Santoso',
                    'password' => 'Wajib diisi untuk pengguna baru',
                    'no_telepon' => '08123456789 (Opsional)',
                    'email' => 'budi.s@sekolah.app (Opsional)',
                ],
            ];
        }

        public function columnWidths(): array
        {
            return [
                'A' => 25, // nip
                'B' => 35, // nama_lengkap
                'C' => 30, // password
                'D' => 25, // no_telepon
                'E' => 30, // email
            ];
        }

        public function styles(Worksheet $sheet)
        {
            $lastColumn = 'E';
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
        
        public function title(): string
        {
            return 'Template Data Guru';
        }
    }