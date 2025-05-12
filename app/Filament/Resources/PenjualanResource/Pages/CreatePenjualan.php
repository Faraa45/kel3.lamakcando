<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

// Tambahan untuk model
use App\Models\Penjualan;
use App\Models\Pembayaran;

// Untuk notifikasi
use Filament\Notifications\Notification;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    /**
     * Handle default status jika belum diisi
     */
    protected function beforeCreate(): void
    {
        $this->data['status'] = $this->data['status'] ?? 'pesan';
    }

    /**
     * Tombol tambahan "Bayar" di bawah form
     */
    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make(), // tombol default "Simpan"

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

    /**
     * Fungsi untuk menyimpan data pembayaran dan update status penjualan
     */
    protected function simpanPembayaran()
    {
        // Cek apakah record sudah tersimpan atau belum
        if (!$this->record) {
            $this->create(); // Simpan form terlebih dahulu jika belum
        }

        $penjualan = $this->record;

        if (!$penjualan) {
            Notification::make()
                ->title('Gagal menyimpan pembayaran!')
                ->danger()
                ->body('Data penjualan tidak ditemukan.')
                ->send();
            return;
        }

        // Simpan ke tabel pembayaran
        Pembayaran::create([
            'penjualan_id'     => $penjualan->id,
            'tgl_bayar'        => now(),
            'jenis_pembayaran' => 'tunai',
            'transaction_time' => now(),
            'gross_amount'     => $penjualan->tagihan ?? 0, // pastikan tidak null
            'order_id'         => $penjualan->no_faktur,
        ]);

        // Update status penjualan
        $penjualan->update(['status' => 'bayar']);

        // Notifikasi berhasil
        Notification::make()
            ->title('Pembayaran Berhasil')
            ->success()
            ->body('Data pembayaran berhasil disimpan dan status penjualan diperbarui.')
            ->send();
    }
}
