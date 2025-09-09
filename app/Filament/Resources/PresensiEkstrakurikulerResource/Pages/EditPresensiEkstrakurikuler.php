<?php

namespace App\Filament\Resources\PresensiEkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use App\Filament\Resources\PresensiEkstrakurikulerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPresensiEkstrakurikuler extends EditRecord
{
    protected static string $resource = PresensiEkstrakurikulerResource::class;

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        $ekstraResource = EkstrakurikulerResource::class;
        $ekstrakurikuler = $this->getRecord()->ekstrakurikuler;
        
        $breadcrumbs = [
            $ekstraResource::getUrl() => $ekstraResource::getBreadcrumb(),
            // ✅ PERBAIKAN: Mengarahkan breadcrumb ke halaman 'edit', bukan 'view'
            $ekstraResource::getUrl('edit', ['record' => $ekstrakurikuler->id]) => $ekstrakurikuler->nama,
            '#' => 'Kelola Presensi',
        ];
        
        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete')
                ->label('Hapus Sesi Ini')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->getRecord()->delete())
                ->after(fn () => $this->redirect($this->getRedirectUrl())),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $ekstrakurikulerId = $this->getRecord()->ekstrakurikuler_id;
        return EkstrakurikulerResource::getUrl('edit', ['record' => $ekstrakurikulerId]);
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->url($this->getRedirectUrl());
    }
}

