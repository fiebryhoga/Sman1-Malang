<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Exports\SiswaTemplateExport;
use App\Imports\SiswaImport; // ✅ Pastikan baris ini ada dan benar
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => Excel::download(new SiswaTemplateExport, 'template_siswa.xlsx')),
            
            Action::make('import_siswa')
                ->label('Import Siswa')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload File Excel')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                        ->disk('local')
                        ->directory('excel-imports'),
                ])
                ->action(function (array $data) {
                    try {
                        Excel::import(new SiswaImport, $data['attachment'], 'local');
                        
                        Notification::make()
                            ->title('Impor Berhasil')
                            ->body('Data siswa telah berhasil diimpor ke database.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Impor Gagal')
                            ->body('Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}