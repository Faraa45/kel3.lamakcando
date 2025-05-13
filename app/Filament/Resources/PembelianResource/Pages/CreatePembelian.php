<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Actions;
use Filament\Notifications\Notification;

use App\Models\Pembelian;
use App\Models\PembelianBahanBaku;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    // Override method untuk menonaktifkan notifikasi default
    protected function getCreatedNotification(): ?Notification
    {
        // Mengembalikan null supaya tidak ada notifikasi default "created"
        return null;
    }

    // Jika status belum diisi, isi default
    protected function beforeCreate(): void
    {
        $this->data['status'] = $this->data['status'] ?? 'pesan';
    }

    // Override agar tidak buat record baru, tapi update yang sudah disimpan dari tombol "Proses"
    protected function handleRecordCreation(array $data): Model
    {
        $pembelianId = $data['pembelian_id'] ?? null;

        if (!$pembelianId) {
            throw new \Exception('Data pembelian belum diproses. Silakan klik tombol "Proses" terlebih dahulu.');
        }

        $pembelian = Pembelian::findOrFail($pembelianId);

        // Update status kalau perlu
        $pembelian->update([
            'status' => $data['status'] ?? 'pesan',
        ]);

        return $pembelian;
    }

    // Optional: notifikasi setelah berhasil
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->success()
            ->send();
    }
}
