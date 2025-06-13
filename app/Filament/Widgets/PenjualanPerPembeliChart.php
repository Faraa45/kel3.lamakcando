<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;
use App\Models\Costumer; // Import model Costumer

class PenjualanPerPembeliChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return 'Total Penjualan Per Pembeli (Tahun ' . date('Y') . ')'; // Judul chart
    }
    
    protected function getType(): string
    {
        return 'bar'; // Menggunakan tipe bar chart
    }

    protected function getData(): array
    {
        $year = now()->year; // Tahun yang ingin ditampilkan

        // Ambil data total penjualan per pembeli
        $customerSales = Penjualan::query()
            ->join('costumer', 'penjualan.costumer_id', '=', 'costumer.id') // Join ke tabel 'costumer'
            ->where('penjualan.status', 'bayar') // Hanya status 'bayar'
            ->whereYear('penjualan.tgl', $year)
            ->selectRaw('costumer.nama_costumer as customer_name, SUM(penjualan.tagihan) as total_belanja')
            ->groupBy('costumer.nama_costumer')
            ->orderByDesc('total_belanja') // Urutkan dari total belanja tertinggi
            ->get();

        $labels = $customerSales->pluck('customer_name'); // Label untuk sumbu X (nama pembeli)
        $data = $customerSales->pluck('total_belanja'); // Data untuk chart (total belanja per pembeli)

        return [
            'datasets' => [
                [
                    'label' => 'Total Belanja',
                    'data' => $data,
                    'backgroundColor' => '#4BC0C0', // Contoh warna
                ],
            ],
            'labels' => $labels,
        ];
    }
}