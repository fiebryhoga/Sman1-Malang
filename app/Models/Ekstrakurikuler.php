<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekstrakurikuler extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * ✅ PERBAIKAN: Relasi diubah menjadi BelongsToMany
     * Sebuah ekstrakurikuler bisa memiliki BANYAK pembina.
     */
    public function pembina(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ekstrakurikuler_user');
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'ekstrakurikuler_siswa');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(PresensiEkstrakurikuler::class);
    }
}