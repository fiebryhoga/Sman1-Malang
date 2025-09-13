<?php

    namespace App\Filament\Resources\UserResource\Pages;

    use App\Filament\Resources\UserResource;
    use App\Exports\GuruTemplateExport; // Impor class template
    use App\Imports\GuruImport;         // Impor class import
    use Filament\Actions;
    use Filament\Actions\Action;
    use Filament\Forms\Components\FileUpload;
    use Filament\Notifications\Notification;
    use Filament\Resources\Pages\ListRecords;
    use Maatwebsite\Excel\Facades\Excel;

    class ListUsers extends ListRecords
    {
        protected static string $resource = UserResource::class;

        protected function getHeaderActions(): array
        {
            return [
                Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => Excel::download(new GuruTemplateExport, 'template_guru.xlsx')),
                
                Action::make('import_guru')
                    ->label('Import Guru')
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
                            Excel::import(new GuruImport, $data['attachment'], 'local');
                            
                            Notification::make()
                                ->title('Impor Berhasil')
                                ->body('Data guru telah berhasil diproses.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Impor Gagal')
                                ->body('Terjadi kesalahan. Pastikan format file Excel sudah benar. Pesan error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Actions\CreateAction::make(),
            ];
        }
    }
