<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailPresensiEkstrakurikuler extends Model
{
    protected $guarded = ['id'];

    public function presensiEkstrakurikuler() {
        return $this->belongsTo(PresensiEkstrakurikuler::class);
    }

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }
}