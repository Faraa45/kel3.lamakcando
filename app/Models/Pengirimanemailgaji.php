<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengirimanemailgaji extends Model
{
    use HasFactory;

    protected $table = 'pengirimanemailgaji'; // Nama tabel eksplisit

    protected $guarded = []; //semua kolom boleh di isi

    // relasi ke tabel penggajian
    public function penggajian()
    {
        return $this->belongsTo(Penggajian::class, 'penggajian_id');
    }
}