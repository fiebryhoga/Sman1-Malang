<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model // Nama class diubah
{
    use HasFactory;
    protected $fillable = ['kode', 'grup', 'deskripsi']; // Kolom diubah

    public function records() // Relasi diubah namanya
    {
        return $this->hasMany(PelanggaranRecord::class);
    }
}