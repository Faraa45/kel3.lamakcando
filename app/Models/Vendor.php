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

    // Mengambil nomor vendor terbaru dan membuat nomor vendor berikutnya
    public static function getNomorVendor()
    {
        // query kode vendor
        $sql = "SELECT IFNULL(MAX(nomor_vendor), 'VDR-000') as nomor_vendor FROM vendor";
        $nomorvendor = DB::select($sql);

        // Mengambil hasil query
        foreach ($nomorvendor as $nmrvdr) {
            $kd = $nmrvdr->nomor_vendor;
        }

        // Mengambil substring tiga digit akhir dan menambah 1
        $noawal = substr($kd, -3);
        $noakhir = $noawal + 1; //menambahkan 1
        $noakhir = 'VDR' . str_pad($noakhir, 3, "0", STR_PAD_LEFT); // format VDR-001
        return $noakhir;
    }

    // Relasi ke BahanBaku
    public function bahanBaku()
    {
        return $this->hasMany(BahanBaku::class, 'nomor_vendor', 'nomor_vendor');
    }

    //Relasi ke pembelian
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'vendor_id', 'nomor_vendor');
    }

}
