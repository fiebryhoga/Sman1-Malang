<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // ✅ PERBAIKAN: Jika NIS kosong, lewati baris ini.
        // Ini akan secara otomatis mengabaikan baris kosong di akhir file.
        if (empty($row['nis'])) {
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
                'kelas_id'      => $kelas?->id,
                'jenis_kelamin' => strtoupper(substr($row['jenis_kelamin'], 0, 1)),
                'agama' => $row['agama'],
                'nisn' => $row['nisn'],
                'angkatan' => $row['angkatan'],
                'nomor_ortu'    => $row['nomor_ortu'] ?? null,
            ]
        );
    }

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 3;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            // Kita gunakan validasi 'nullable' agar baris kosong bisa dilewati oleh method model()
            'nis' => 'nullable|numeric', 
            'nama_lengkap' => 'nullable|string',
            'nama_kelas' => 'nullable|exists:kelas,nama',
            'jenis_kelamin' => 'nullable|string|in:L,P,l,p,Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'nisn' => 'nullable|numeric',
            'angkatan' => 'nullable|numeric|digits:4',
            'nomor_ortu' => 'nullable|numeric',
        ];
    }
} 