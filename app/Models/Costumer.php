<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Costumer extends Model
{
    use HasFactory;
    protected $table = 'costumer';
    protected $guarded = [];

    public static function getKodeCostumer()
    {
        // query kode costumer
        $sql = "SELECT IFNULL(MAX(kode_costumer), 'CUST-000') as kode_costumer
                FROM costumer";
        $kodecostumer = DB::select($sql);

        // cacah hasilnya
        foreach ($kodecostumer as $kdcust) {
            $kd = $kdcust->kode_costumer; 
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd, -3);
        $noakhir = $noawal + 1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'CUST' . str_pad($noakhir, 3, "0", STR_PAD_LEFT); //menyambung dengan string CUST-001
        return $noakhir;

    }

    // relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'costumer_id');
    }
}
