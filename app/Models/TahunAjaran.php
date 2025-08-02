<?php
// app/Models/TahunAjaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * The "booted" method of the model.
     * Ini akan menambahkan logika otomatis saat model disimpan.
     */
    protected static function booted(): void
    {
        static::saving(function (TahunAjaran $tahunAjaran) {
            // Jika tahun ajaran yang sedang disimpan ditandai sebagai aktif
            if ($tahunAjaran->is_active) {
                // Maka, non-aktifkan semua tahun ajaran lainnya.
                self::where('id', '!=', $tahunAjaran->id)->update(['is_active' => false]);
            }
        });
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}