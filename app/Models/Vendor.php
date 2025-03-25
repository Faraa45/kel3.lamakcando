<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vendor extends Model
{
    use HasFactory;
    protected $table = 'vendor';
    protected $guarded = [];

    public static function getNomorVendor()
    {
        // query kode costumer
        $sql = "SELECT IFNULL(MAX(nomor_vendor), 'VDR-000') as nomor_vendor
                FROM vendor";
        $nomorvendor = DB::select($sql);

        // cacah hasilnya
        foreach ($nomorvendor as $nmrvdr) {
            $kd = $nmrvdr->nomor_vendor; 
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd, -3);
        $noakhir = $noawal + 1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'VDR' . str_pad($noakhir, 3, "0", STR_PAD_LEFT); //menyambung dengan string CUST-001
        return $noakhir;

    }
}