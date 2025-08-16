<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JamPelajaran extends Model
{
    use HasFactory;
    protected $fillable = ['jam_ke'];

    // Tambahkan relasi many-to-many ke JadwalMengajar
    public function jadwalMengajar(): BelongsToMany
{
    return $this->belongsToMany(JadwalMengajar::class, 'jadwal_mengajar_jam_pelajaran');
}
}