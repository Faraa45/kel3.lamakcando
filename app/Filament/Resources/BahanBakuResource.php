<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanBakuResource\Pages;
use App\Models\BahanBaku;
use App\Models\Vendor;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class BahanBakuResource extends Resource
{
    protected static ?string $model = BahanBaku::class;

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $pluralModelLabel = 'Bahan Baku';
    public static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'nama_vendor')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $kodeBaru = BahanBaku::getKodeBahan($state);
                            $set('kode_bahan', $kodeBaru);
                        }
                    }),

                TextInput::make('kode_bahan')
                    ->label('Kode Bahan')
                    ->required()
                    ->readOnly(),

                TextInput::make('nama_bahan')
                    ->label('Nama Bahan')
                    ->required(),

                TextInput::make('satuan')
                    ->label('Satuan')
                    ->required(),

                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bahan')->label('Kode'),
                TextColumn::make('nama_bahan')->label('Nama'),
                TextColumn::make('vendor.nama_vendor')->label('Vendor'),
                TextColumn::make('satuan')->label('Satuan'),
                TextColumn::make('stok')->label('Stok'),
                TextColumn::make('harga_satuan')->label('Harga Satuan')->money('IDR', true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBahanBakus::route('/'),
            'create' => Pages\CreateBahanBaku::route('/create'),
            'edit' => Pages\EditBahanBaku::route('/{record}/edit'),
        ];
    }
}