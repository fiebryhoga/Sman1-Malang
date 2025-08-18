<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensiHarian;
use App\Models\PresensiHarian;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ApiPresensiController extends Controller
{
    public function checkIn(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nis' => 'required|string|exists:siswas,nis',
                'tanggal' => 'required|date_format:Y-m-d',
                'jam_masuk' => 'required|date_format:H:i:s',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Data tidak valid.', 'errors' => $e->errors()], 422);
        }

        $sesi = PresensiHarian::where('tanggal', $validatedData['tanggal'])
            ->where('status', 'buka')
            ->first();

        if (!$sesi) {
            return response()->json(['message' => 'Sesi presensi untuk tanggal ini tidak ditemukan atau sudah ditutup.'], 404);
        }

        if (Carbon::parse($validatedData['jam_masuk']) > Carbon::parse($sesi->batas_presensi)) {
            return response()->json(['message' => 'Waktu presensi sudah berakhir.'], 403);
        }

        $siswa = Siswa::where('nis', $validatedData['nis'])->first();

        $detailPresensi = DetailPresensiHarian::where('presensi_harian_id', $sesi->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if (!$detailPresensi) {
             return response()->json(['message' => 'Siswa tidak terdaftar pada sesi presensi ini.'], 404);
        }

        if ($detailPresensi->status === 'hadir') {
            return response()->json([
                'message' => 'Presensi siswa ' . $siswa->nama_lengkap . ' sudah tercatat sebelumnya.',
                'data' => $detailPresensi,
            ], 409);
        }

        // ✅ PERBAIKAN UTAMA: Gunakan tanggal dan jam_masuk dari data yang dikirim, bukan now()
        $waktuPresensi = Carbon::parse($validatedData['tanggal'] . ' ' . $validatedData['jam_masuk']);
        
        $jamMasukSesi = Carbon::createFromFormat('Y-m-d H:i:s', $sesi->tanggal->format('Y-m-d') . ' ' . $sesi->jam_masuk);
        
        $selisihDetik = $waktuPresensi->diffInSeconds($jamMasukSesi, false);
        
        if ($selisihDetik < 0) {
            $keterangan = 'Terlambat ' . $jamMasukSesi->diff($waktuPresensi)->format('%h jam %i menit');
        } elseif ($selisihDetik > 0) {
            $keterangan = 'Lebih Awal ' . $waktuPresensi->diff($jamMasukSesi)->format('%h jam %i menit');
        } else {
            $keterangan = 'Tepat Waktu';
        }

        $detailPresensi->update([
            'status' => 'hadir',
            'waktu_presensi' => $waktuPresensi, // Simpan waktu dari data yang dikirim
            'keterangan_waktu' => $keterangan,
        ]);

        return response()->json([
            'message' => 'Presensi untuk siswa ' . $siswa->nama_lengkap . ' berhasil dicatat.',
            'data' => $detailPresensi->refresh(),
        ], 200);
    }
}