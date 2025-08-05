<?php

// app/Models/DisciplinaryPointCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryPointCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'points'];

    public function disciplinaryPointRecords()
    {
        return $this->hasMany(DisciplinaryPointRecord::class);
    }
}