<?php
// app/Models/KehadiranSiswa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KehadiranSiswa extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_siswa';
    protected $guarded = [];

    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}