<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DetailPresensiHarian extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'waktu_presensi' => 'datetime',
    ];

    public function presensiHarian() {
        return $this->belongsTo(PresensiHarian::class);
    }

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }

    protected static function booted(): void
    {
        static::updating(function (DetailPresensiHarian $detail) {
            if ($detail->isDirty('status')) {
                if ($detail->status === 'hadir') {
                    // ✅ PERBAIKAN UTAMA:
                    // Hanya isi waktu presensi jika belum ada nilainya (artinya diubah dari admin panel).
                    // Jika dari API, nilainya sudah ada dan tidak akan diubah.
                    if (is_null($detail->waktu_presensi)) {
                        $detail->waktu_presensi = now();
                    }
                    
                    // Gunakan waktu presensi final (baik dari API atau dari now()) untuk perhitungan.
                    $waktuPresensi = $detail->waktu_presensi;
                    $jamMasukSesi = Carbon::parse($detail->presensiHarian->tanggal->format('Y-m-d') . ' ' . $detail->presensiHarian->jam_masuk);
                    
                    $selisihDetik = $waktuPresensi->diffInSeconds($jamMasukSesi, false);

                    if ($selisihDetik < 0) {
                        $keterangan = 'Terlambat ' . $jamMasukSesi->diff($waktuPresensi)->format('%h jam %i menit');
                    } elseif ($selisihDetik > 0) {
                        $keterangan = 'Lebih Awal ' . $waktuPresensi->diff($jamMasukSesi)->format('%h jam %i menit');
                    } else {
                        $keterangan = 'Tepat Waktu';
                    }
                    
                    $detail->keterangan_waktu = $keterangan;

                } else {
                    $detail->waktu_presensi = null;
                    $detail->keterangan_waktu = null;
                }
            }
        });
    }
}