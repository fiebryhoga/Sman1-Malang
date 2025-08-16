<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JadwalMengajar extends Model
{
    use HasFactory;
    protected $fillable = ['mata_pelajaran_id', 'kelas_id', 'user_id'];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // Tambahkan relasi many-to-many ke JamPelajaran
    public function jamPelajaran(): BelongsToMany
{
    return $this->belongsToMany(JamPelajaran::class, 'jadwal_mengajar_jam_pelajaran');
}
}