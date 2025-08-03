<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Logika untuk mengisi created_by secara otomatis
    protected static function booted(): void
    {
        static::creating(function (Presensi $presensi) {
            if (auth()->check()) {
                $presensi->created_by = auth()->id();
            }
        });
    }

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

    // Relasi baru untuk mengambil data pembuat presensi
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detailPresensi()
    {
        return $this->hasMany(DetailPresensi::class);
    }
}
