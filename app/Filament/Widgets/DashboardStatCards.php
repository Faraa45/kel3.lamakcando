<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;

use App\Models\Penjualan;
use App\Models\Coa;
use App\Models\Costumer;
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\Pembelian; // Pastikan Anda mengimpor model Pembelian

use Illuminate\Support\Number;
use Illuminate\Support\Carbon;

class DashboardStatCards extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil filter tanggal jika ada
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Cek jenis pelanggan bisnis atau bukan
        $isBusinessCustomersOnly = $this->filters['businessCustomersOnly'] ?? null;
        $businessCustomerMultiplier = match (true) {
            boolval($isBusinessCustomersOnly) => 2 / 3,
            blank($isBusinessCustomersOnly) => 1,
            default => 1 / 3,
        };

        // Hitung selisih hari
        $diffInDays = $startDate ? $startDate->diffInDays($endDate) : 0;

        // Hitung metrik dinamis
        //$revenue = (int) (($startDate ? ($diffInDays * 137) : 192100) * $businessCustomerMultiplier);
        $newCustomers = (int) (($startDate ? ($diffInDays * 7) : 1340) * $businessCustomerMultiplier);
        $newOrders = (int) (($startDate ? ($diffInDays * 13) : 3543) * $businessCustomerMultiplier);

        // Format angka
        $formatNumber = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 2) . 'k';
            }

            return Number::format($number / 1000000, 2) . 'm';
        };

        // Hitung persentase kehadiran pegawai
        $totalAbsensi = Absensi::query()
            ->when($startDate, fn ($query) => $query->whereBetween('tanggal', [$startDate, $endDate]))
            ->count();

        $absenHadir = Absensi::query()
            ->when($startDate, fn ($query) => $query->whereBetween('tanggal', [$startDate, $endDate]))
            ->where('status', 'hadir')
            ->count();

        $persenKehadiran = $totalAbsensi > 0
            ? round(($absenHadir / $totalAbsensi) * 100, 2)
            : 0;

        // Calculate revenue from the database
        $revenue = Penjualan::query()
            ->where('status', 'bayar') // Assuming 'bayar' means paid
            ->when($startDate, function ($query, $startDate) use ($endDate) {
                return $query->whereBetween('tanggal_penjualan', [$startDate, $endDate]); // Assuming 'tanggal_penjualan' is the sales date
            })
            ->sum('tagihan'); // Assuming 'tagihan' is the total amount

        $revenue = (int) ($revenue * $businessCustomerMultiplier); // Apply the business customer multiplier


        // Calculate total purchases
        $totalPurchases = Penjualan::count();

        return [
            Stat::make('Total Pembeli', Costumer::count())
                ->description('Jumlah pembeli terdaftar'),

            Stat::make('Jumlah Pegawai', Pegawai::count())
                ->description('Total jumlah pegawai')
                ->descriptionIcon('heroicon-o-users'),
                
            Stat::make('Total Transaksi Penjualan', Penjualan::count())
                ->description('Jumlah transaksi  penjualan'),

            Stat::make('Kehadiran Pegawai', $persenKehadiran . '%')
                ->description('Persentase kehadiran pegawai')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([90, 92, 85, 95, 88, 93, 97])
                ->color('success'),
            Stat::make('Total Penjualan', rupiah(
                Penjualan::query()
                    ->where('status', 'bayar')
                    ->sum('tagihan')
            ))
                ->description('Jumlah transaksi terbayar (Rupiah)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Keuntungan', rupiah(
                Penjualan::query()
                    ->join('penjualan_menu', 'penjualan.id', '=', 'penjualan_menu.penjualan_id')
                    ->where('status', 'bayar')
                    ->selectRaw('SUM((penjualan_menu.harga_jual - penjualan_menu.harga_beli) * penjualan_menu.jml) as total_penjualan')
                    ->value('total_penjualan')
            ))
                ->description('Jumlah keuntungan (Rupiah)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            
            Stat::make('Total Pembelian', rupiah(
                Pembelian::query()
                    ->sum('total_tagihan')
            ))
                ->description('Jumlah total pembelian ')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }

    protected function getCards(): array
    {
        return [
            // Tambahan kartu jika diperlukan
        ];
    }
}
