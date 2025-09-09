<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke kelas tempat siswa berada.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(KehadiranSiswa::class);
    }

    /**
     * Relasi ke catatan pelanggaran yang dimiliki siswa.
     */
    public function pelanggaranRecords(): HasMany
    {
        return $this->hasMany(PelanggaranRecord::class);
    }

    /**
     * Relasi ke ekstrakurikuler yang diikuti oleh siswa.
     */
    public function ekstrakurikulers(): BelongsToMany
    {
        return $this->belongsToMany(Ekstrakurikuler::class, 'ekstrakurikuler_siswa');
    }

    /**
     * Catatan: Relasi 'kehadiran' di bawah ini sepertinya tidak lagi relevan
     * dengan sistem presensi mapel dan presensi harian yang sudah ada.
     * Anda bisa menghapusnya jika sudah tidak digunakan.
     */
    // public function kehadiran(): HasMany
    // {
    //     return $this->hasMany(KehadiranSiswa::class);
    // }
}
