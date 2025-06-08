<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBahanBaku extends Model
{
    protected $fillable = [
        'menu_id',
        'bahan_baku_id',
        'jumlah',
        'penjualan_id',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
