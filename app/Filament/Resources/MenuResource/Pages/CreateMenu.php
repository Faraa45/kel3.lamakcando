<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function handleRecordCreation(array $data): \App\Models\Menu
    {
        // Pisahkan data bahanBaku dari data menu utama
        $bahanBakuData = $data['bahanBaku'] ?? [];
        unset($data['bahanBaku']);

        // Buat record Menu terlebih dahulu tanpa relasi bahanBaku
        $menu = static::getModel()::create($data);

        // Sinkronkan relasi bahanBaku dengan data pivot (jumlah)
        $syncData = [];
        foreach ($bahanBakuData as $bahan) {
            $syncData[$bahan['bahan_baku_id']] = ['jumlah' => $bahan['jumlah']];
        }

        $menu->bahanBaku()->sync($syncData);

        return $menu;
    }
}
