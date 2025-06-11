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
        $penjualan = $this->record ?? \App\Models\Penjualan::latest()->first();

        // Simpan ke tabel pembayaran
        \App\Models\Pembayaran::create([
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
        \Filament\Notifications\Notification::make()
            ->title('Pembayaran Berhasil!')
            ->success()
            ->send();
    }
}