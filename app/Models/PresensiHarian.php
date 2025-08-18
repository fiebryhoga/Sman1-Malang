<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PresensiHarian extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * ✅ TAMBAHKAN INI
     * Memberitahu Eloquent untuk mengubah kolom 'tanggal' menjadi objek Carbon secara otomatis.
     */
    protected $casts = [
        'tanggal' => 'date',
    ];
    
    public function details(): HasMany
    {
        return $this->hasMany(DetailPresensiHarian::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (PresensiHarian $presensiHarian) {
            if (auth()->check()) {
                $presensiHarian->created_by = auth()->id();
            }
        });
    }
}