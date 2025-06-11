<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\Costumer;
use App\Models\Menu;
use App\Models\PenjualanMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action as FormAction;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\BahanBaku;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Penjualan';
    protected static ?string $navigationGroup = 'Transaksi';
    public static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // STEP 1
                    Wizard\Step::make('Pesanan')
                        ->schema([
                            Forms\Components\Section::make('Faktur')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([
                                    TextInput::make('no_faktur')
                                        ->default(fn () => Penjualan::getKodeFaktur())
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly(),
                                    DateTimePicker::make('tgl')->default(now()),
                                    Select::make('costumer_id')
                                        ->label('Costumer')
                                        ->options(Costumer::pluck('nama_costumer', 'id')->toArray())
                                        ->required()
                                        ->placeholder('Pilih Costumer'),
                                    TextInput::make('tagihan')->default(0)->hidden(),
                                    TextInput::make('status')->default('pesan')->hidden(),
                                ])
                                ->columns(3)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),

                    // STEP 2
                    Wizard\Step::make('Pilih Menu')
                        ->schema([
                            Repeater::make('items')
                                ->relationship('penjualanMenu')
                                ->schema([
                                    Select::make('menu_id')
                                        ->label('Menu')
                                        ->options(Menu::pluck('nama_menu', 'id')->toArray())
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set) {
                                            $menu = Menu::find($state);
                                            $set('harga_beli', $menu?->harga_menu ?? 0);
                                            $set('harga_jual', $menu?->harga_menu * 1.2 ?? 0);
                                        }),
                                    TextInput::make('harga_beli')->label('Harga Beli')->numeric()->readonly()->hidden()->dehydrated(),
                                    TextInput::make('harga_jual')->label('Harga Menu')->numeric()->readonly()->dehydrated(),
                                    TextInput::make('jml')->label('Jumlah')->default(1)->reactive()->required(),
                                    DatePicker::make('tgl')->default(today())->required(),
                                ])
                                ->columns(4)
                                ->addable()
                                ->deletable()
                                ->reorderable()
                                ->createItemButtonLabel('Tambah Item')
                                ->minItems(1)
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\Actions::make([
                                FormAction::make('Proses')
                                    ->label('Proses Pesanan')
                                    ->action(function ($get, $set) {
                                        try {
                                            DB::beginTransaction();

                                            // Simpan Penjualan
                                            $penjualan = Penjualan::updateOrCreate(
                                                ['no_faktur' => $get('no_faktur')],
                                                [
                                                    'tgl' => $get('tgl'),
                                                    'costumer_id' => $get('costumer_id'),
                                                    'status' => 'pesan',
                                                    'tagihan' => 0,
                                                ]
                                            );

                                            // Simpan Menu dan Kurangi Stok
                                            foreach ($get('items') as $item) {
                                                \App\Models\PenjualanMenu::updateOrCreate(
                                                    [
                                                        'penjualan_id' => $penjualan->id,
                                                        'menu_id' => $item['menu_id'],
                                                    ],
                                                    [
                                                        'harga_beli' => $item['harga_beli'],
                                                        'harga_jual' => $item['harga_jual'],
                                                        'jml' => $item['jml'],
                                                        'tgl' => $item['tgl'],
                                                    ]
                                                );
                                            }

                                            // Hitung Tagihan
                                            $totalTagihan = PenjualanMenu::where('penjualan_id', $penjualan->id)
                                                ->sum(DB::raw('harga_jual * jml'));

                                            $penjualan->update(['tagihan' => $totalTagihan]);
                                            $set('tagihan', $totalTagihan);

                                            DB::commit();
                                        } catch (Exception $e) {
                                            DB::rollBack();
                                            throw $e;
                                        }
                                    }),
                            ])
                            ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),

                    // STEP 3
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                ->content(fn (Get $get) => view('filament.components.penjualan-table', [
                                    'pembayarans' => Penjualan::where('no_faktur', $get('no_faktur'))->get()
                                ])),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->maxWidth('7xl')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                Tables\Columns\TextColumn::make('costumer.nama_costumer')->label('Nama Costumer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('tagihan')
                    ->formatStateUsing(fn ($state) => rupiah($state))
                    ->alignment('end')
                    ->sortable(),
                Tables\Columns\TextColumn::make('penjualanMenu_sum_jml')
                    ->label('Jumlah Stok Dibeli')
                    ->getStateUsing(fn ($record) => $record->penjualanMenu->sum('jml'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d-m-Y H:i:s')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Hapus Terpilih')
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->requiresConfirmation()
                    ->color('danger'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
