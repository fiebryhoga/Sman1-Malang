<?php
// app/Models/User.php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne; // Pastikan ini ada
use Illuminate\Database\Eloquent\Relations\HasMany; // Pastikan ini ada
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Pastikan ini ada

class User extends Authenticatable implements FilamentUser, HasAvatar // <-- Tambahkan HasAvatar
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url', 
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return Storage::disk('public')->url($this->avatar_url);
        }

        // Fallback jika tidak ada foto, menggunakan UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=0284C7';
    }

    public function kelasWali(): HasOne
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }

    public function mataPelajaranDiajar(): BelongsToMany
    {
        // Mata pelajaran yang diajar oleh guru ini di kelas tertentu
        return $this->belongsToMany(MataPelajaran::class, 'kelas_mata_pelajaran', 'user_id', 'mata_pelajaran_id');
    }

    public function kelasDiajar(): BelongsToMany
    {
        // Kelas yang diajar oleh guru ini
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran', 'user_id', 'kelas_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Untuk awal, semua user bisa akses panel. Nanti bisa diatur lebih lanjut.
        return true;
    }
}