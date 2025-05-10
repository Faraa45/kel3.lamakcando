<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanMenu extends Model
{
    use HasFactory;

    protected $table = 'penjualan_menu';
    protected $fillable = ['penjualan_id', 'menu_id', 'harga_beli', 'harga_jual', 'jml', 'tgl'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}