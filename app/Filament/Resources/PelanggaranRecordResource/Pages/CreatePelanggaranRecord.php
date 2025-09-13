<?php

namespace App\Filament\Resources\PelanggaranRecordResource\Pages;

use App\Filament\Resources\PelanggaranRecordResource;
use App\Http\Controllers\Api\PelanggaranNotificationController; // ✅ 1. Impor Controller
use App\Services\BaileysService; // ✅ 2. Impor BaileysService
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreatePelanggaranRecord extends CreateRecord
{
    protected static string $resource = PelanggaranRecordResource::class;

    /**
     * ✅ 3. PERBAIKAN: Panggil controller setelah record dibuat
     */
    protected function afterCreate(): void
    {
        try {
            // Ambil record yang baru saja dibuat
            $record = $this->getRecord();
            
            // Siapkan controller dan service
            $notificationController = new PelanggaranNotificationController();
            $baileysService = app(BaileysService::class);
            
            // Panggil method untuk mengirim notifikasi
            $notificationController->sendNotification($record, $baileysService);

        } catch (\Exception $e) {
            // Catat error jika proses notifikasi gagal
            Log::error('Gagal memicu notifikasi pelanggaran dari CreateRecord: ' . $e->getMessage());
        }
    }
}