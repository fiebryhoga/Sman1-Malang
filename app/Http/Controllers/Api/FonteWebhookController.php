<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FonteWebhookController extends Controller
{
    /**
     * Menerima dan memproses pesan masuk dari Fonte.
     */
    public function handle(Request $request)
    {
        $sender = $request->input('sender');
        $message = $request->input('message');

        Log::info('Pesan masuk dari Fonte:', $request->all());

        // Pisahkan pesan menjadi kata kunci dan parameter (Nama atau NIS)
        $parts = explode(' ', $message, 2);
        $keyword = strtolower($parts[0] ?? '');
        $parameter = $parts[1] ?? '';

        $replyMessage = '';

        if ($keyword === 'presensi' && !empty($parameter)) {
            // Jika parameter berupa angka, cari berdasarkan NIS. Jika tidak, cari berdasarkan nama.
            if (is_numeric($parameter)) {
                $replyMessage = $this->getPresensiSiswaByNis($parameter);
            } else {
                $replyMessage = $this->getPresensiSiswaByName($parameter);
            }
        } else {
            $replyMessage = "Format pesan salah. \n\nKetik: *presensi [Nama Lengkap Siswa]* \nAtau: *presensi [NIS_SISWA]*";
        }

        $this->sendReply($sender, $replyMessage);

        return response()->json(['status' => 'success']);
    }

    /**
     * Mencari data presensi siswa berdasarkan Nama.
     */
    private function getPresensiSiswaByName($nama)
    {
        $siswas = Siswa::where('nama_lengkap', 'like', '%' . $nama . '%')->get();

        if ($siswas->isEmpty()) {
            return "Siswa dengan nama yang mengandung *'{$nama}'* tidak ditemukan.";
        }

        if ($siswas->count() > 1) {
            $response = "Ditemukan beberapa siswa dengan nama yang cocok. Mohon gunakan NIS untuk hasil yang lebih spesifik.\n\n";
            foreach ($siswas as $siswa) {
                $response .= "- {$siswa->nama_lengkap} (NIS: {$siswa->nis})\n";
            }
            return $response;
        }

        // Jika hanya satu siswa yang ditemukan, format pesannya
        return $this->formatPresensiMessage($siswas->first());
    }

    /**
     * Mencari data presensi siswa berdasarkan NIS.
     */
    private function getPresensiSiswaByNis($nis)
    {
        $siswa = Siswa::where('nis', $nis)->first();

        if (!$siswa) {
            return "Siswa dengan NIS *{$nis}* tidak ditemukan.";
        }
        
        return $this->formatPresensiMessage($siswa);
    }

    /**
     * Mengambil dan memformat pesan rekap presensi untuk seorang siswa.
     */
    private function formatPresensiMessage(Siswa $siswa)
    {
        $presensiHariIni = DetailPresensi::where('siswa_id', $siswa->id)
            ->whereHas('presensi', function ($query) {
                $query->whereDate('tanggal', Carbon::today('Asia/Jakarta'));
            })
            ->with('presensi.mataPelajaran')
            ->get();

        if ($presensiHariIni->isEmpty()) {
            return "Belum ada data presensi untuk siswa *{$siswa->nama_lengkap}* pada hari ini.";
        }

        $reply = "Rekap Presensi untuk *{$siswa->nama_lengkap}* hari ini:\n\n";
        foreach ($presensiHariIni as $detail) {
            $status = ucwords($detail->status);
            $mapel = $detail->presensi->mataPelajaran->nama;
            $waktu = $detail->presensi->created_at->format('H:i');
            $reply .= "- *{$mapel}* ({$waktu}): *{$status}*\n";
        }

        return $reply;
    }

    /**
     * Mengirim pesan balasan ke API Fonte.
     */
    private function sendReply($target, $message)
    {
        $token = env('FONNTE_API_TOKEN');

        if (!$token) {
            Log::error('FONNTE_API_TOKEN tidak ditemukan di file .env');
            return;
        }

        Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
        ]);
    }
}
