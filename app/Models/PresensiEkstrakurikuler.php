<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PresensiEkstrakurikuler extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['tanggal' => 'date'];

    public function ekstrakurikuler() {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function details() {
        return $this->hasMany(DetailPresensiEkstrakurikuler::class);
    }

    public function pencatat() {
        return $this->belongsTo(User::class, 'created_by');
    }
}