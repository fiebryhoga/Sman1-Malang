<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPresensi extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function presensi()
    {
        return $this->belongsTo(Presensi::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
