<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DisciplinaryPointRecord;
use App\Services\BaileysService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisciplinaryPointController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Pastikan request memiliki record_id
        $recordId = $request->input('record_id');
        if (!$recordId) {
            return response()->json(['status' => 'error', 'message' => 'Record ID is required.'], 400);
        }

        // Ambil record dari database
        $record = DisciplinaryPointRecord::with(['siswa', 'category'])->find($recordId);

        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Record not found.'], 404);
        }

        $siswa = $record->siswa;
        $category = $record->category;
        
        if (!$siswa->nomor_ortu) {
            Log::info('Nomor HP orang tua tidak ditemukan, notifikasi tidak dikirim.', ['siswa_id' => $siswa->id]);
            return response()->json(['status' => 'warning', 'message' => 'Nomor HP orang tua tidak ditemukan.'], 200);
        }

        $message = "Assalamualaikum Bapak/Ibu,\n\n";
        $message .= "Kami dari pihak sekolah ingin memberitahukan bahwa siswa/i atas nama *{$siswa->nama_lengkap}* dari kelas *{$siswa->kelas->nama}* mendapat penambahan poin kedisiplinan sebesar *{$category->points}* poin.\n\n";
        $message .= "Hal ini dikarenakan siswa/i tersebut melakukan pelanggaran: *{$category->name}*.\n\n";
        $message .= "Poin total saat ini adalah *{$siswa->disciplinary_points}* dari batas maksimal 200 poin.\n\n";
        $message .= "Kami mohon kerja sama Bapak/Ibu dalam membimbing siswa/i. Terima kasih.";

        $baileysService = app(BaileysService::class);
        $success = $baileysService->sendMessage($siswa->nomor_ortu, $message);

        if ($success) {
            return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil dikirim.']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Gagal mengirim notifikasi.'], 500);
    }
}