<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $guarded = [];

    public static function getNoMenu()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(no_menu), 'MN-000') as no_menu
                FROM menu ";
        $nomenu = DB::select($sql);

        // cacah hasilnya
        foreach ($nomenu as $nmn) {
            $kd = $nmn->no_menu;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'MN-'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;

    }

     // Dengan mutator ini, setiap kali data harga_menu dikirim ke database, koma akan otomatis dihapus.
    public function setHargaMenuAttribute($value)
    {
         // Hapus koma (,) dari nilai sebelum menyimpannya ke database
        $this->attributes['harga_menu'] = str_replace('.', '', $value);
    }

     // Relasi dengan tabel relasi many to many nya
    public function penjualanMenu()
    {
        return $this->hasMany(PenjualanMenu::class, 'menu_id');
    }

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'menu_bahan_baku', 'menu_id', 'bahan_baku_id')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

}