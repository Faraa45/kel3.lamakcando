<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';
    protected $guarded = [];

    public static function getKodeFaktur()
    {
        $sql = "SELECT IFNULL(MAX(no_faktur_pembelian), 'PB-0000000') as no_faktur_pembelian FROM pembelian";
        $kodefaktur = DB::select($sql);

        foreach ($kodefaktur as $kdpmbl) {
            $kd = $kdpmbl->no_faktur_pembelian;
        }

        $noawal = substr($kd, -7);
        $noakhir = $noawal + 1;
        $noakhir = 'PB-' . str_pad($noakhir, 7, "0", STR_PAD_LEFT);

        return $noakhir;
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function pembelianBahanBaku()
    {
        return $this->hasMany(PembelianBahanBaku::class, 'pembelian_id');
    }
}
