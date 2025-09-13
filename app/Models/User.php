<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'nip',
        'name',
        'no_telepon',
        'email',
        'password',
        'avatar_url', 
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return Storage::disk('public')->url($this->avatar_url);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=0284C7';
    }
    
    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->roles->isEmpty()) {
                $user->assignRole('Guru Mata Pelajaran');
            }
        });
    }

    /**
     * ✅ TAMBAHKAN INI: Mutator untuk memformat nomor telepon secara otomatis.
     * Fungsi ini akan berjalan setiap kali Anda mencoba mengisi atribut 'no_telepon'.
     *
     * @param  string|null  $value
     * @return void
     */
    public function setNoTeleponAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['no_telepon'] = null;
            return;
        }

        // 1. Hapus semua karakter selain angka
        $nomor = preg_replace('/[^0-9]/', '', $value);

        // 2. Jika diawali '62', ganti dengan '0'
        if (substr($nomor, 0, 2) === '62') {
            $nomor = '0' . substr($nomor, 2);
        }
        // 3. Jika diawali '8' (tanpa '0' di depannya), tambahkan '0'
        elseif (substr($nomor, 0, 1) === '8') {
            $nomor = '0' . $nomor;
        }
        
        $this->attributes['no_telepon'] = $nomor;
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
public function ekstrakurikulersDiampu(): BelongsToMany
    {
        return $this->belongsToMany(Ekstrakurikuler::class, 'ekstrakurikuler_user');
    }
}