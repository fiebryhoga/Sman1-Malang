<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * PENTING: Memberitahu Laravel cara menangani tipe data kolom.
     */
    protected $casts = [
        'tanggal' => 'date',
    ];

    // Logika untuk mengisi created_by secara otomatis
    protected static function booted(): void
    {
        static::creating(function (Presensi $presensi) {
            if (auth()->check()) {
                $presensi->created_by = auth()->id();
            }
        });
    }

    // ... sisa relasi Anda sudah benar ...
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detailPresensi()
    {
        return $this->hasMany(DetailPresensi::class);
    }
}