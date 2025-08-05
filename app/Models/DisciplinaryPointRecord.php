<?php

// app/Models/DisciplinaryPointRecord.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryPointRecord extends Model
{
    use HasFactory;
    protected $fillable = ['siswa_id', 'disciplinary_point_category_id', 'photo'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function category()
    {
        return $this->belongsTo(DisciplinaryPointCategory::class, 'disciplinary_point_category_id');
    }
}