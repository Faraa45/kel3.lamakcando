<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    //
    protected $table = 'penggajian';
    protected $fillable = [
        'no_slip_gaji',
        'pegawai_id',
        'tgl',
        'jumlah_hadir',
        'gaji_per_hari',
        'total_gaji',
        'status_pembayaran',
        'periode_awal',
        'periode_akhir',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
    // Model Penggajian
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'pegawai_id');
    }

    // Di model Penggajian
    public function hitungJumlahHadir($pegawaiId, $periodeAwal, $periodeAkhir)
    {
        return \App\Models\Absensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
            ->count();
    }
    // Di model Penggajian.php}




protected static function booted()

    protected static function booted()
    {
    static::creating(function ($penggajian) {
        if (!$penggajian->no_slip_gaji) {
            $last = static::latest()->first();
            $nextId = $last ? $last->id + 1 : 1;
            $penggajian->no_slip_gaji = 'PGJ-' . now()->format('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }
    });
    }
}

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Penggajian extends Model

{
    static::created(function ($penggajian) {
        $penggajian->no_slip_gaji = 'PGJ-' . now()->format('Ymd') . '-' . str_pad($penggajian->id, 3, '0', STR_PAD_LEFT);
        $penggajian->save();
    });
}
}