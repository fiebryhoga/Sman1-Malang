<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Relasi many-to-many ke model Kelas.
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran')
                    ->withPivot('user_id') // Ini penting untuk mengambil ID guru pengampu
                    ->withTimestamps();
    }
}