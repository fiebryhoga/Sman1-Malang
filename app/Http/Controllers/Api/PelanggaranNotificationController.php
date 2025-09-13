<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranRecord;
use App\Services\BaileysService;

class PelanggaranNotificationController extends Controller
{
    public function sendNotification(PelanggaranRecord $record, BaileysService $baileysService)
    {
        $record->load(['siswa.kelas.waliKelas', 'pelanggaran', 'user']);

        $siswa = $record->siswa;
        $pelanggaran = $record->pelanggaran;
        $waliKelas = $siswa->kelas?->waliKelas;
        $pencatat = $record->user?->name ?? 'Sistem';

        // Menyiapkan catatan, jika kosong akan diisi dengan strip (-)
        $catatan = !empty($record->catatan) ? $record->catatan : '-';

        $successParent = true;
        $successWali = true;

        // 1. Kirim notifikasi ke Orang Tua
        if ($siswa->nomor_ortu) {
            // ✅ PERUBAHAN: Format pesan baru untuk orang tua
            $messageToParent = "Yth. Orang Tua/Wali dari an. *{$siswa->nama_lengkap}* ({$siswa->kelas->nama}) NIS *{$siswa->nis}*," .
                               "\n\nKami informasikan bahwa siswa/i telah mendapat catatan pelanggaran:" .
                               "\n- {$pelanggaran->deskripsi} (Kode: {$pelanggaran->kode})" .
                               "\n\ncatatan lainnya : {$catatan}" .
                               "\n\nKami mohon kerja sama Bapak/Ibu dalam membimbing siswa/i. Terima kasih." .
                               "\n*SMAN 1 Malang*";

            $successParent = $baileysService->sendMessage($siswa->nomor_ortu, $messageToParent);
        }

        // 2. Kirim notifikasi ke Wali Kelas
        if ($waliKelas && $waliKelas->no_telepon) {
            // ✅ PERUBAHAN: Format pesan baru untuk wali kelas
            $messageToWaliKelas = "Info Pelanggaran Siswa," .
                                  "\n\nSiswa an. *{$siswa->nama_lengkap}* - (NIS: {$siswa->nis}) dari kelas perwalian Anda (*{$siswa->kelas->nama}*) telah mendapat catatan pelanggaran baru:" .
                                  "\n- {$pelanggaran->deskripsi}" .
                                  "\n\ncatatan lainnya : {$catatan}" .
                                  "\n\nMohon untuk ditindaklanjuti. Terima kasih.";

            $successWali = $baileysService->sendMessage($waliKelas->no_telepon, $messageToWaliKelas);
        }

        if ($successParent && $successWali) {
            return response()->json(['message' => 'Semua notifikasi berhasil dikirim.']);
        }

        return response()->json(['message' => 'Sebagian atau semua notifikasi gagal dikirim.'], 500);
    }
}