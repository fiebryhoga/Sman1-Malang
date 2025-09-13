<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranRecord extends Model
{
    use HasFactory;
    protected $fillable = ['siswa_id', 'pelanggaran_id', 'photo', 'catatan', 'user_id'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function pelanggaran() { return $this->belongsTo(Pelanggaran::class); }
    public function user() { return $this->belongsTo(User::class); }
    
    protected static function booted(): void
    {
        static::creating(function (PelanggaranRecord $record) {
            if (auth()->check()) {
                $record->user_id = auth()->id();
            }
        });
    }
}