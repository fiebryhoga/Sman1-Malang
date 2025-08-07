<?php



namespace App\Filament\Resources\RekapPresensiResource\Pages;

use App\Filament\Resources\RekapPresensiResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListRekapPresensis extends ListRecords
{
    protected static string $resource = RekapPresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(function () {
                    $filters = $this->filters ?? [];
                    $queryString = http_build_query($filters);
                    return route('excel.export', ['filters' => $queryString]);
                })
                ->openUrlInNewTab(),
        ];
    }
}