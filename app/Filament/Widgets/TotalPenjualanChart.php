<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;
// use App\Models\PenjualanBarang;

class TotalPenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Total Penjualan'; // Judul widget chart

    // Mendapatkan data untuk chart
    protected function getData(): array
    {
        // Ambil data total penjualan berdasarkan rumus (harga_jual - harga_beli) * jumlah
        $data = Penjualan::query()
            ->join('penjualan_menu', 'penjualan.id', '=', 'penjualan_menu.penjualan_id')
            ->join('menu', 'penjualan_menu.menu_id', '=', 'menu.id')
            ->where('penjualan.status', 'bayar') // Hanya status 'bayar'
            ->selectRaw('menu.nama_menu, SUM(penjualan_menu.harga_jual * penjualan_menu.jml) as total_penjualan')
            ->groupBy('menu.nama_menu')
            ->get()
            ->map(function ($penjualan) {
                return [
                    'nama_menu' => $penjualan->nama_menu,
                    'total_penjualan' => $penjualan->total_penjualan,
                ];
            });
            // dd($data); // untuk melihat data sebelum dikirim ke chart

        // Pastikan data ada sebelum dikirim ke chart
        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Mengembalikan data dalam format yang dibutuhkan untuk chart
        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data->pluck('total_penjualan')->toArray(), // Data untuk chart
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('nama_menu')->toArray(), // Label untuk sumbu X
        ];
    }

    // Jenis chart yang digunakan, misalnya bar chart
    protected function getType(): string
    {
        return 'bar'; // Tipe chart bisa diganti sesuai kebutuhan, seperti 'line', 'pie', dll.
    }
}

