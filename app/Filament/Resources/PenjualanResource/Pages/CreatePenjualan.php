<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

// tambahan untuk akses ke penjualabarang
use App\Models\Penjualan;
use App\Models\PenjualanMenu;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

// untuk notifikasi
use Filament\Notifications\Notification;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    //penanganan kalau status masih kosong 
    protected function beforeCreate(): void
    {
        $this->data['status'] = $this->data['status'] ?? 'pesan';
    }

    // tambahan untuk simpan
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('bayar')
                ->label('Bayar')
                ->color('success')
                ->action(fn () => $this->simpanPembayaran())
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembayaran')
                ->modalDescription('Apakah Anda yakin ingin menyimpan pembayaran ini?')
                ->modalButton('Ya, Bayar'),
        ];
    }

    // penanganan
    protected function simpanPembayaran()
    {
        $penjualan = $this->record ?? Penjualan::latest()->first();

        // Tambahkan data ke penjualan_menu jika belum ada
        if (PenjualanMenu::where('penjualan_id', $penjualan->id)->count() == 0 && isset($this->data['items'])) {
            foreach ($this->data['items'] as $item) {
                PenjualanMenu::create([
                    'penjualan_id' => $penjualan->id,
                    'menu_id' => $item['menu_id'],
                    'harga_beli' => $item['harga_beli'] ?? 0,
                    'harga_jual' => $item['harga_jual'] ?? 0,
                    'jml' => $item['jml'],
                    'tgl' => $item['tgl'] ?? now(),
                ]);
            }
        }

        // Kurangi stok bahan baku sesuai menu yang dibeli
        $items = PenjualanMenu::where('penjualan_id', $penjualan->id)->get();
        foreach ($items as $item) {
            $menu = \App\Models\Menu::with('bahanBaku')->find($item->menu_id);
            if ($menu) {
                foreach ($menu->bahanBaku as $bahan) {
                    $jumlahPerMenu = $bahan->pivot->jumlah;
                    $jumlahTotal = $jumlahPerMenu * $item->jml;
                    if ($bahan->stok < $jumlahTotal) {
                        throw new \Exception("Stok bahan baku '{$bahan->nama_bahan}' tidak cukup.");
                    }
                    $bahan->decrement('stok', $jumlahTotal);
                }
            }
        }

        // Simpan ke tabel pembayaran
        Pembayaran::create([
            'penjualan_id' => $penjualan->id,
            'tgl_bayar'    => now(),
            'jenis_pembayaran' => 'tunai',
            'transaction_time' => now(),
            'gross_amount'       => $penjualan->tagihan,
            'order_id' => $penjualan->no_faktur,
        ]);

        // Update status penjualan jadi "dibayar"
        $penjualan->update(['status' => 'bayar']);

        // Notifikasi sukses
        Notification::make()
            ->title('Pembayaran Berhasil!')
            ->success()
            ->send();
    }
}
