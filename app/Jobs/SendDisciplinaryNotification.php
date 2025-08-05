<?php

namespace App\Jobs;

use App\Models\DisciplinaryPointRecord;
use App\Services\BaileysService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDisciplinaryNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recordId;
    public $totalPoints;

    public function __construct($recordId, $totalPoints)
    {
        $this->recordId = $recordId;
        $this->totalPoints = $totalPoints;
    }

    public function handle(): void
    {
        // --- PERBAIKAN DI SINI ---
        // Muat relasi siswa dan kelas menggunakan dot notation
        $record = DisciplinaryPointRecord::with(['siswa.kelas', 'category'])->find($this->recordId);
        // --- AKHIR PERBAIKAN ---

        if (!$record) {
            Log::error("DisciplinaryPointRecord with ID {$this->recordId} not found for notification.");
            return;
        }

        $siswa = $record->siswa;
        $category = $record->category;

        if (!$siswa->nomor_ortu) {
            Log::info('Nomor HP orang tua siswa tidak ditemukan, notifikasi tidak dikirim.', ['siswa_id' => $siswa->id]);
            return;
        }

        $message = "Assalamualaikum Bapak/Ibu,\n\n";
        $message .= "Kami dari pihak sekolah ingin memberitahukan bahwa siswa/i atas nama *{$siswa->nama_lengkap}* dari kelas *{$siswa->kelas->nama}* mendapat penambahan poin kedisiplinan sebesar *{$category->points}* poin.\n\n";
        $message .= "Hal ini dikarenakan siswa/i tersebut melakukan pelanggaran: *{$category->name}*.\n\n";
        $message .= "Poin total saat ini adalah *{$this->totalPoints}* dari batas maksimal 200 poin.\n\n";
        $message .= "Kami mohon kerja sama Bapak/Ibu dalam membimbing siswa/i. Terima kasih.";

        $baileysService = app(BaileysService::class);
        $baileysService->sendMessage($siswa->nomor_ortu, $message);
    }
}