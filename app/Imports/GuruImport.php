<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        if (empty($row['nip'])) {
            return null;
        }
        
        // Cari user berdasarkan NIP, atau siapkan instance baru jika tidak ada
        $user = User::firstOrNew(['nip' => $row['nip']]);

        // ✅ PERBAIKAN: Mengisi semua data termasuk yang opsional
        $user->name = $row['nama_lengkap'];
        $user->no_telepon = $row['no_telepon'] ?? null; // Ambil no_telepon jika ada
        $user->email = $row['email'] ?? null; // Ambil email jika ada

        // Hanya isi password jika user baru atau jika kolom password di Excel diisi
        // Ini mencegah password user lama ter-reset jika kolomnya kosong di Excel
        if (!$user->exists || !empty($row['password'])) {
            $user->password = Hash::make($row['password']);
        }
        
        // Kembalikan objek User, Laravel Excel akan menanganinya (save/update)
        return $user;
    }

    public function headingRow(): int
    {
        return 3;
    }

    public function rules(): array
    {
        // Validasi disederhanakan, kita hanya perlu memastikan NIP dan Nama ada
        return [
            'nip' => 'required',
            'nama_lengkap' => 'required',
            'no_telepon' => 'nullable|numeric',
            'email' => 'nullable|email',
            'password' => 'nullable|min:8', // Password opsional saat update
        ];
    }
}