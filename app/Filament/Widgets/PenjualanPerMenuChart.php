<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;
use App\Models\Menu; // Import model Menu
// use App\Models\PenjualanBarang; // Ini tidak lagi digunakan, karena akan pakai PenjualanMenu

use Carbon\Carbon; // Ini mungkin tidak lagi diperlukan, tapi biarkan saja jika ada kebutuhan lain.

class PenjualanPerMenuChart extends ChartWidget
{
    protected static ?string $heading = null; // Biarkan null, akan diatur di getHeading

    public function getHeading(): string
    {
        return 'Penjualan Per Menu (Tahun ' . date('Y') . ')'; // Mengubah judul chart
    }
    
    // Mengubah jenis chart menjadi 'bar' jika belum
    protected function getType(): string
    {
        return 'bar'; // Mengubah jenis chart menjadi bar
    }

    // Mendapatkan data untuk chart
    protected function getData(): array
    {
        $year = now()->year; // Tahun yang ingin ditampilkan

        // Ambil data total penjualan per menu
        // Join ke tabel 'penjualan_menu' dan 'menu'
        $menuSales = Penjualan::query()
            ->join('penjualan_menu', 'penjualan.id', '=', 'penjualan_menu.penjualan_id')
            ->join('menu', 'penjualan_menu.menu_id', '=', 'menu.id')
            ->where('penjualan.status', 'bayar') // Hanya status 'bayar'
            ->whereYear('penjualan.tgl', $year)
            ->selectRaw('menu.nama_menu as menu_name, SUM(penjualan_menu.harga_jual * penjualan_menu.jml) as total_penjualan_menu')
            ->groupBy('menu.nama_menu')
            ->orderByDesc('total_penjualan_menu') // Urutkan dari penjualan tertinggi
            ->get();

        $labels = $menuSales->pluck('menu_name'); // Label untuk sumbu X (nama menu)
        $data = $menuSales->pluck('total_penjualan_menu'); // Data untuk chart (total penjualan per menu)

        // Mengembalikan data dalam format yang dibutuhkan untuk chart
        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $labels, // Label untuk sumbu X
        ];
    }

    // Jenis chart yang digunakan, misalnya bar chart
    // protected function getType(): string
    // {
    //     return 'line'; // Awalnya 'line', diubah jadi 'bar' di fungsi getType()
    // }
}