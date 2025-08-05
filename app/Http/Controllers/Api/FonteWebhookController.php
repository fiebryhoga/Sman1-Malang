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

        $parts = explode(' ', $message, 2);
        $keyword = strtolower($parts[0] ?? '');
        $parameter = $parts[1] ?? '';

        $replyMessage = '';

        if ($keyword === 'presensi' && !empty($parameter)) {
            if (is_numeric($parameter)) {
                $replyMessage = $this->getPresensiSiswaByNis($parameter);
            } else {
                $replyMessage = $this->getPresensiSiswaByName($parameter);
            }
        } else {
            $replyMessage = "Format pesan salah. \n\nKetik: *presensi [Nama Lengkap Siswa]* \nAtau: *presensi [NIS SISWA]*";
        }

        $this->sendReply($sender, $replyMessage);

        return response()->json(['status' => 'success']);
    }

    /**
     * Mencari data presensi siswa berdasarkan Nama.
     */
    private function getPresensiSiswaByName($nama)
    {
        // Menambahkan with('kelas') untuk efisiensi query
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

    /**
     * Mencari data presensi siswa berdasarkan NIS.
     */
    private function getPresensiSiswaByNis($nis)
    {
        // Menambahkan with('kelas') untuk efisiensi query
        $siswa = Siswa::with('kelas')->where('nis', $nis)->first();

        if (!$siswa) {
            return "Siswa dengan NIS *{$nis}* tidak ditemukan.";
        }
        
        return $this->formatPresensiMessage($siswa);
    }

    /**
     * PERUBAHAN UTAMA: Mengambil dan memformat pesan rekap presensi untuk seorang siswa.
     */
    private function formatPresensiMessage(Siswa $siswa)
    {
        // Ambil informasi hari dan tanggal saat ini dalam Bahasa Indonesia
        $sekarang = Carbon::now('Asia/Jakarta');
        $hariIni = $sekarang->locale('id')->translatedFormat('l');
        $tanggalIni = $sekarang->locale('id')->translatedFormat('d F Y');

        $presensiHariIni = DetailPresensi::where('siswa_id', $siswa->id)
            ->whereHas('presensi', function ($query) use ($sekarang) {
                $query->whereDate('tanggal', $sekarang);
            })
            ->with('presensi.mataPelajaran')
            ->get();

        // Jika tidak ada data presensi
        if ($presensiHariIni->isEmpty()) {
            $pesanKosong = "Assalamualaikum Bapak/Ibu,\n\n";
            $pesanKosong .= "Dengan hormat, kami informasikan bahwa hingga saat ini belum terdapat data kehadiran untuk siswa/i atas nama *{$siswa->nama_lengkap}* pada hari *{$hariIni}, {$tanggalIni}*.\n\n";
            $pesanKosong .= "Terima kasih.";
            return $pesanKosong;
        }

        // Jika ada data presensi, buat pesan lengkap
        $namaSiswa = $siswa->nama_lengkap;
        $namaKelas = $siswa->kelas->nama ?? 'Informasi Kelas Tidak Ditemukan'; // Fallback jika relasi kelas tidak ada

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
            // Mengambil waktu dari data presensi, bukan waktu saat ini
            $waktu = Carbon::parse($detail->presensi->created_at)->format('H:i');
            $reply .= "- Jam {$waktu} - {$mapel}: *{$status}*\n";
        }

        $reply .= "\nDemikian informasi yang dapat kami sampaikan. Terima kasih atas perhatiannya.";

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