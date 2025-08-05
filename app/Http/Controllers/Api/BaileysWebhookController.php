<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\DetailPresensi;
use App\Services\BaileysService; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BaileysWebhookController extends Controller
{
    protected $baileysService;

    // Gunakan dependency injection untuk BaileysService
    public function __construct(BaileysService $baileysService)
    {
        $this->baileysService = $baileysService;
    }

    public function handle(Request $request)
    {
        $sender = $request->input('sender');
        $message = $request->input('message');

        Log::info('Pesan masuk dari Baileys:', $request->all());

        $replyMessage = $this->processMessage($message);
        
        // Kirim balasan melalui API Baileys
        $this->baileysService->sendMessage($sender, $replyMessage);
        
        // Respons ke server Node.js
        return response()->json(['status' => 'success']);
    }

    private function processMessage($message)
    {
        $parts = explode(' ', $message, 2);
        $keyword = strtolower($parts[0] ?? '');
        $parameter = $parts[1] ?? '';

        if ($keyword === 'presensi' && !empty($parameter)) {
            if (is_numeric($parameter)) {
                return $this->getPresensiSiswaByNis($parameter);
            } else {
                return $this->getPresensiSiswaByName($parameter);
            }
        }
        
        return "Format pesan salah. \n\nKetik: *presensi [Nama Lengkap Siswa]* \nAtau: *presensi [NIS SISWA]*";
    }

    private function getPresensiSiswaByName($nama)
    {
        $siswas = Siswa::with('kelas')->where('nama_lengkap', 'like', '%' . $nama . '%')->get();

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

        return $this->formatPresensiMessage($siswas->first());
    }

    private function getPresensiSiswaByNis($nis)
    {
        $siswa = Siswa::with('kelas')->where('nis', $nis)->first();

        if (!$siswa) {
            return "Siswa dengan NIS *{$nis}* tidak ditemukan.";
        }
        
        return $this->formatPresensiMessage($siswa);
    }

    private function formatPresensiMessage(Siswa $siswa)
    {
        $sekarang = Carbon::now('Asia/Jakarta');
        $hariIni = $sekarang->locale('id')->translatedFormat('l');
        $tanggalIni = $sekarang->locale('id')->translatedFormat('d F Y');

        $presensiHariIni = DetailPresensi::where('siswa_id', $siswa->id)
            ->whereHas('presensi', function ($query) use ($sekarang) {
                $query->whereDate('tanggal', $sekarang);
            })
            ->with('presensi.mataPelajaran')
            ->get();
            
        // ... (Logika format pesan yang sama dengan kode Fonte Anda) ...
        // Karena logika ini sudah ada di kode Anda, saya tidak perlu menuliskannya lagi.
        // Anda bisa menyalinnya langsung dari `FonteWebhookController`.

        if ($presensiHariIni->isEmpty()) {
            $pesanKosong = "Assalamualaikum Bapak/Ibu,\n\n";
            $pesanKosong .= "Dengan hormat, kami informasikan bahwa hingga saat ini belum terdapat data kehadiran untuk siswa/i atas nama *{$siswa->nama_lengkap}* pada hari *{$hariIni}, {$tanggalIni}*.\n\n";
            $pesanKosong .= "Terima kasih.";
            return $pesanKosong;
        }

        $namaSiswa = $siswa->nama_lengkap;
        $namaKelas = $siswa->kelas->nama ?? 'Informasi Kelas Tidak Ditemukan';

        $reply = "Assalamualaikum Bapak/Ibu,\n\n";
        $reply .= "Dengan hormat, kami sampaikan rekapitulasi kehadiran siswa/i pada:\n";
        $reply .= "*Hari, Tanggal:* {$hariIni}, {$tanggalIni}\n\n";
        $reply .= "Atas nama:\n";
        $reply .= "*Nama Siswa:* {$namaSiswa}\n";
        $reply .= "*Kelas:* {$namaKelas}\n\n";
        $reply .= "Berikut adalah rincian kehadirannya:\n";

        foreach ($presensiHariIni as $detail) {
            $status = ucwords($detail->status);
            $mapel = $detail->presensi->mataPelajaran->nama;
            $waktu = Carbon::parse($detail->presensi->created_at)->format('H:i');
            $reply .= "- Jam {$waktu} - {$mapel}: *{$status}*\n";
        }

        $reply .= "\nDemikian informasi yang dapat kami sampaikan. Terima kasih atas perhatiannya.";

        return $reply;
    }
}