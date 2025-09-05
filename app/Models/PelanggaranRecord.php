<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelanggaranRecord extends Model
{
    use HasFactory;
    protected $fillable = ['siswa_id', 'pelanggaran_id', 'photo', 'catatan', 'user_id']; // Tambahkan user_id

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }

    public function pelanggaran() {
        return $this->belongsTo(Pelanggaran::class);
    }

    // ✅ TAMBAHKAN INI: Mendefinisikan relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // ✅ TAMBAHKAN INI: Mengisi user_id secara otomatis
    protected static function booted(): void
    {
        static::creating(function (PelanggaranRecord $record) {
            if (auth()->check()) {
                $record->user_id = auth()->id();
            }
        });
    }
}