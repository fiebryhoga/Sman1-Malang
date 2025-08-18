<?php

namespace App\Http\Controllers;

use App\Exports\JurnalGuruExport;
use App\Exports\RekapPresensiExport;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    /**
     * Method untuk membuat file Excel rekap presensi per siswa.
     */
    public function exportRekapPresensi(Request $request)
    {
        // Set locale agar format tanggal (nama hari & bulan) menjadi Bahasa Indonesia
        \Carbon\Carbon::setLocale('id_ID');

        $allFilters = $request->query('filters', []);
        $kelasId = $allFilters['kelas_id']['value'] ?? null;
        $mapelId = $allFilters['mata_pelajaran_id']['value'] ?? null;
        $tanggalMulai = $allFilters['tanggal']['tanggal_mulai'] ?? null;
        $tanggalSelesai = $allFilters['tanggal']['tanggal_selesai'] ?? null;

        if (!$kelasId || !$mapelId) {
            abort(400, 'Filter Kelas dan Mata Pelajaran wajib diisi untuk mengunduh file.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        $presensiQuery = Presensi::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['detailPresensi', 'guru'])
            ->orderBy('tanggal', 'asc');

        if ($tanggalMulai) {
            $presensiQuery->whereDate('tanggal', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $presensiQuery->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        $presensis = $presensiQuery->get();

        if ($presensis->isEmpty()) {
            abort(404, 'Tidak ada data presensi untuk diekspor pada filter yang Anda pilih.');
        }

        $siswas = Siswa::where('kelas_id', $kelasId)->orderBy('nama_lengkap', 'asc')->get();
        $namaGuru = $presensis->first()->guru->name ?? auth()->user()->name;

        $rekapData = [];
        $rekapPerSiswa = [];
        foreach ($siswas as $siswa) {
            $rekapPerSiswa[$siswa->id] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
            foreach ($presensis as $presensi) {
                $detailsBySiswaId = $presensi->detailPresensi->keyBy('siswa_id');
                $detail = $detailsBySiswaId->get($siswa->id);
                $status = $detail ? strtoupper(substr($detail->status, 0, 1)) : 'A';
                $rekapData[$siswa->id][$presensi->tanggal->format('Y-m-d')] = $status;
                
                if (isset($rekapPerSiswa[$siswa->id][$status])) {
                    $rekapPerSiswa[$siswa->id][$status]++;
                }
            }
        }

        $bulan = $presensis->first()->tanggal->translatedFormat('F Y');
        if ($presensis->last()->tanggal->format('F Y') != $presensis->first()->tanggal->format('F Y')) {
            $bulan = $presensis->first()->tanggal->translatedFormat('F Y') . ' - ' . $presensis->last()->tanggal->translatedFormat('F Y');
        }
        
        $namaFile = 'Rekap Presensi ' . $kelas->nama . ' - ' . $mataPelajaran->nama . '.xlsx';
        
        return Excel::download(new RekapPresensiExport(
            $siswas, $presensis, $rekapData, $rekapPerSiswa, $kelas->nama, $mataPelajaran->nama, $bulan, $namaGuru
        ), $namaFile);
    }

    /**
     * Method untuk membuat file Excel jurnal mengajar guru.
     */
    public function exportJurnalGuru(Request $request)
    {
        // ✅ Set locale agar format tanggal (nama hari & bulan) menjadi Bahasa Indonesia
        \Carbon\Carbon::setLocale('id_ID');

        $allFilters = $request->query('filters', []);
        $kelasId = $allFilters['kelas_id']['value'] ?? null;
        $mapelId = $allFilters['mata_pelajaran_id']['value'] ?? null;
        $tanggalMulai = $allFilters['tanggal']['tanggal_mulai'] ?? null;
        $tanggalSelesai = $allFilters['tanggal']['tanggal_selesai'] ?? null;

        if (!$kelasId || !$mapelId) {
            abort(400, 'Filter Kelas dan Mata Pelajaran wajib diisi untuk mengunduh file.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $presensiQuery = Presensi::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->withCount([
                'detailPresensi as hadir_count' => fn ($query) => $query->where('status', 'hadir'),
                'detailPresensi as sakit_count' => fn ($query) => $query->where('status', 'sakit'),
                'detailPresensi as izin_count' => fn ($query) => $query->where('status', 'izin'),
                'detailPresensi as alpha_count' => fn ($query) => $query->where('status', 'alpha'),
            ])
            ->with('guru')
            ->orderBy('tanggal', 'asc');

        if ($tanggalMulai) {
            $presensiQuery->whereDate('tanggal', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $presensiQuery->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        $jurnals = $presensiQuery->get();

        if ($jurnals->isEmpty()) {
            abort(404, 'Tidak ada data jurnal untuk diekspor pada filter yang Anda pilih.');
        }
        
        $namaGuru = $jurnals->first()->guru->name ?? auth()->user()->name;
        $bulan = $jurnals->first()->tanggal->translatedFormat('F Y');
        if ($jurnals->last()->tanggal->format('F Y') != $jurnals->first()->tanggal->format('F Y')) {
            $bulan = $jurnals->first()->tanggal->translatedFormat('F Y') . ' - ' . $jurnals->last()->tanggal->translatedFormat('F Y');
        }

        $namaFile = 'Jurnal Guru ' . $kelas->nama . ' - ' . $mataPelajaran->nama . '.xlsx';

        return Excel::download(new JurnalGuruExport(
            $jurnals, $kelas->nama, $mataPelajaran->nama, $bulan, $namaGuru
        ), $namaFile);
    }
}