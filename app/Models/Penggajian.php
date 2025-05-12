<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajian'; // Nama tabel eksplisit

    protected $guarded = [];

    public static function getNoPenggajian()
    {
        // Query untuk mendapatkan nomor penggajian terakhir
        $sql = "SELECT IFNULL(MAX(no_penggajian), 'GJ-0000000') as no_penggajian FROM penggajian";
        $result = DB::select($sql);

        // Ambil hasilnya
        $kode = $result[0]->no_penggajian;

        // Ambil 7 digit terakhir
        $noawal = substr($kode, -7);
        $noakhir = (int)$noawal + 1;

        // Format nomor akhir dengan prefix 'GJ-' dan 7 digit
        $noakhir = 'GJ-' . str_pad($noakhir, 7, "0", STR_PAD_LEFT);

        return $noakhir;
    }

    // Relasi ke tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
