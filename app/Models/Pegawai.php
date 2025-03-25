<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $guarded = []; 
    
    public static function getIdPegawai()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(id_pegawai), 'PGW-000') as id_pegawai
                FROM pegawai ";
        $idpegawai = DB::select($sql);

        // cacah hasilnya
        foreach ($idpegawai as $pgw) {
            $kd = $pgw->id_pegawai;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'PGW'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;
}
}