<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = ['pegawai_id', 'no_absensi', 'status', 'tgl', 'keterangan'];

    protected $fillable = ['pegawai_id', 'no_absensi', 'status', 'tgl', 'durasi_jam_kerja'];


    public static function getNoAbsensi()
    {
        // Query untuk mendapatkan nomor absensi terakhir
        $sql = "SELECT IFNULL(MAX(no_absensi), 'ABSN-000') as no_absensi FROM absensi";
        $result = DB::select($sql);

        // Ambil hasilnya
        $noabsensi = $result[0]->no_absensi;

        // Mengambil substring tiga digit akhir dari string ABSN-000
        $noawal = substr($noabsensi, -3); // Ambil 3 digit terakhir
        $noakhir = (int) $noawal + 1; // Tambah 1 untuk nomor berikutnya

        // Format nomor akhir dengan prefix 'ABSN-' dan 3 digit
        $noakhir = 'ABSN-' . str_pad($noakhir, 3, "0", STR_PAD_LEFT); // Format menjadi ABSN-001

        return $noakhir;
    }
// App\Models\Absensi.php

public function pegawai()
{
    return $this->belongsTo(Pegawai::class, 'pegawai_id');
}

    
}
