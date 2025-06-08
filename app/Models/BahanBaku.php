<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';
    protected $guarded = [];

    /**
     * Relasi ke model Vendor.
     * Setiap bahan baku dimiliki oleh satu vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Relasi ke model Produksi (jika bahan baku digunakan dalam proses produksi).
     */
    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'bahan_baku_id');
    }

    /**
     * Generate kode bahan baku otomatis berdasarkan vendor.
     * Format: BHK-<VendorID>-<3 digit nomor>
     * Contoh: BHK-01-001
     */
    public static function getKodeBahan($vendorId = null)
    {
        $prefix = 'BHK';

        // Jika vendor ID disediakan, tambahkan ke prefix
        if ($vendorId) {
            $prefix .= '-' . str_pad($vendorId, 2, '0', STR_PAD_LEFT);
        }

        // Query kode terakhir untuk vendor terkait
        $sql = "SELECT IFNULL(MAX(kode_bahan), '{$prefix}-000') as kode_bahan 
                FROM bahan_baku 
                WHERE kode_bahan LIKE '{$prefix}-%'";

        $data = DB::select($sql);

        // Ambil kode terakhir dan hitung increment
        $kode = $data[0]->kode_bahan ?? "{$prefix}-000";
        $noUrut = substr($kode, -3);
        $noBaru = (int)$noUrut + 1;
        $kodeBaru = $prefix . '-' . str_pad($noBaru, 3, '0', STR_PAD_LEFT);

        return $kodeBaru;
    }

    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan_baku')
            ->withPivot('jumlah')
            ->withTimestamps();
    }
}