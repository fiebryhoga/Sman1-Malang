<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!isset($row['nis'])) {
            return null;
        }

        $kelas = !empty($row['nama_kelas'])
            ? Kelas::where('nama', $row['nama_kelas'])->first()
            : null;

        return Siswa::updateOrCreate(
            [
                'nis' => $row['nis'],
            ],
            [
                'nama_lengkap'  => $row['nama_lengkap'],
                'jenis_kelamin' => !empty($row['jenis_kelamin']) ? strtoupper(substr($row['jenis_kelamin'], 0, 1)) : null,
                'nomor_ortu'    => $row['nomor_ortu'] ?? null,
                'kelas_id'      => $kelas?->id,
            ]
        );
    }

    /**
     * ✅ TAMBAHKAN INI
     * Method ini memberitahu Laravel Excel bahwa judul kolom (header)
     * dimulai dari baris ke-3, bukan baris ke-1.
     */
    public function headingRow(): int
    {
        return 3;
    }
}