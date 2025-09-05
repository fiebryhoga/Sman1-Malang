<?php
// app/Models/Siswa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(KehadiranSiswa::class);
    }



    // public function pelanggaranRecords()
    // {
    //     return $this->hasMany(PelanggaranRecord::class);
    // }

    public function pelanggaranRecords(): HasMany
    {
        return $this->hasMany(PelanggaranRecord::class);
    }


    // // Accessor untuk menghitung total poin kedisiplinan
    // public function getDisciplinaryPointsAttribute()
    // {
    //     return $this->disciplinaryPointRecords()
    //                 ->join('disciplinary_point_categories', 'disciplinary_point_records.disciplinary_point_category_id', '=', 'disciplinary_point_categories.id')
    //                 ->sum('disciplinary_point_categories.points');
    // }
}