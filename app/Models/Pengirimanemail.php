<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengirimanemail extends Model
{
    use HasFactory;

    protected $table = 'pengirimanemail'; // Nama tabel eksplisit

    protected $guarded = []; // Semua kolom boleh diisi

    // relasi ke tabel pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}
