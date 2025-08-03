<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiApiController extends Controller
{
    public function cekPresensi($nis)
    {
        $siswa = Siswa::where('nis', $nis)->first();

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $presensiHariIni = DetailPresensi::where('siswa_id', $siswa->id)
            ->whereHas('presensi', function ($query) {
                $query->whereDate('tanggal', Carbon::today('Asia/Jakarta'));
            })
            ->with('presensi.mataPelajaran')
            ->get();

        if ($presensiHariIni->isEmpty()) {
            return response()->json([
                'nama_siswa' => $siswa->nama_lengkap,
                'status' => 'Belum ada data presensi untuk hari ini.',
            ]);
        }

        // Format data untuk respons
        $rekap = $presensiHariIni->map(function ($detail) {
            return [
                'mata_pelajaran' => $detail->presensi->mataPelajaran->nama,
                'status' => $detail->status,
                'waktu' => $detail->presensi->created_at->format('H:i'),
            ];
        });

        return response()->json([
            'nama_siswa' => $siswa->nama_lengkap,
            'rekap' => $rekap,
        ]);
    }
}
