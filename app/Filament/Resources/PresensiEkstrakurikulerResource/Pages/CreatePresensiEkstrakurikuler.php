<?php

namespace App\Filament\Resources\PresensiEkstrakurikulerResource\Pages;

use App\Filament\Resources\EkstrakurikulerResource;
use App\Filament\Resources\PresensiEkstrakurikulerResource;
use App\Models\Ekstrakurikuler;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePresensiEkstrakurikuler extends CreateRecord
{
    protected static string $resource = PresensiEkstrakurikulerResource::class;

    public ?int $ekstrakurikulerId = null; 
    public ?Ekstrakurikuler $ekstrakurikuler = null;

    public function mount(): void
    {
        $this->ekstrakurikulerId = request()->query('ekstrakurikuler_id');
        $this->ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikulerId);
        abort_if(!$this->ekstrakurikuler, 404);
        parent::mount();
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        $ekstraResource = EkstrakurikulerResource::class;
        
        $breadcrumbs = [
            $ekstraResource::getUrl() => $ekstraResource::getBreadcrumb(),
            $ekstraResource::getUrl('edit', ['record' => $this->ekstrakurikuler->id]) => $this->ekstrakurikuler->nama,
            '#' => 'Buat Sesi Presensi',
        ];
        
        return $breadcrumbs;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['ekstrakurikuler_id'] = $this->ekstrakurikulerId;
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $presensiEkstra = $this->getRecord();
        $anggotaIds = $this->ekstrakurikuler->siswas()->pluck('siswas.id');
        $detailData = [];
        foreach ($anggotaIds as $siswaId) {
            $detailData[] = [
                'presensi_ekstrakurikuler_id' => $presensiEkstra->id,
                'siswa_id' => $siswaId,
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($detailData)) {
            DB::table('detail_presensi_ekstrakurikulers')->insert($detailData);
        }
    }
    
    /**
     * ✅ PERBAIKAN: Mengarahkan ke halaman 'edit' DARI RECORD YANG BARU DIBUAT
     */
    protected function getRedirectUrl(): string
    {
        // $this->getRecord() akan mengambil sesi presensi yang baru saja dibuat
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    /**
     * Tombol "Cancel" akan tetap mengarahkan kembali ke halaman detail Ekstrakurikuler.
     */
    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->url(
            EkstrakurikulerResource::getUrl('edit', ['record' => $this->ekstrakurikulerId])
        );
    }
}