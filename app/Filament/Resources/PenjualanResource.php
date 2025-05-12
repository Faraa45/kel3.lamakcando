<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Wizard; //untuk menggunakan wizard
use Filament\Forms\Components\TextInput; //untuk penggunaan text input
use Filament\Forms\Components\DateTimePicker; //untuk penggunaan date time picker
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; //untuk penggunaan select
use Filament\Forms\Components\Repeater; //untuk penggunaan repeater
use Filament\Tables\Columns\TextColumn; //untuk tampilan tabel
use Filament\Forms\Components\Placeholder; //untuk menggunakan text holder
use Filament\Forms\Get; //menggunakan get 
use Filament\Forms\Set; //menggunakan set 
use Filament\Forms\Components\Hidden; //menggunakan hidden field
use Filament\Tables\Filters\SelectFilter; //untuk menambahkan filter

// model
use App\Models\Costumer;
use App\Models\Menu;
use App\Models\Pembayaran;
use App\Models\PenjualanMenu;

// DB
use Illuminate\Support\Facades\DB;
// untuk dapat menggunakan action
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Actions\Action as TableAction;


// tambahan untuk tombol unduh pdf
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

// use App\Filament\Resources\ActionResource;


class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

        // merubah nama label menjadi Pembeli
    protected static ?string $navigationLabel = 'Penjualan';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Wizard
                Wizard::make([
                    Wizard\Step::make('Pesanan')
                        ->schema([
                            // section 1
                            Forms\Components\Section::make('Faktur') // Bagian pertama
                                // ->description('Detail Menu')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([ 
                                    TextInput::make('no_faktur')
                                        ->default(fn () => Penjualan::getKodeFaktur()) // Ambil default dari method getKodeMenu
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly() // Membuat field menjadi read-only
                                    ,
                                    DateTimePicker::make('tgl')->default(now()) // Nilai default: waktu sekarang
                                    ,
                                    Select::make('costumer_id')
                                        ->label('Costumer')
                                        ->options(Costumer::pluck('nama_costumer', 'id')->toArray()) // Mengambil data dari tabel
                                        ->required()
                                        ->placeholder('Pilih Costumer') // Placeholder default
                                    ,
                                    TextInput::make('tagihan')
                                        ->default(0) // Nilai default
                                        ->hidden()
                                    ,
                                    TextInput::make('status')
                                        ->default('pesan') // Nilai default status pemesanan adalah pesan/bayar/kirim
                                        ->hidden()
                                    ,
                                ])
                                ->collapsible() // Membuat section dapat di-collapse
                                ->columns(3)
                            ,
                        ]),
                    Wizard\Step::make('Pilih Menu')
                    ->schema([
                        // 
                            // untuk menambahkan repeater
                            Repeater::make('items')
                            ->relationship('penjualanMenu')
                            // ->live()
                            ->schema([
                                Select::make('menu_id')
                                        ->label('Menu')
                                        ->options(Menu::pluck('nama_menu', 'id')->toArray())
                                        // Mengambil data dari tabel
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems() //agar komponen item tidak berulang
                                        ->reactive() // Membuat field reactive
                                        ->placeholder('Pilih Menu') // Placeholder default
                                        ->afterStateUpdated(function ($state, $set) {
                                            $menu = Menu::find($state);
                                            $set('harga_beli', $menu ? $menu->harga_menu : 0);
                                            $set('harga_jual', $menu ? $menu->harga_menu*1.2 : 0);
                                        })
                                        ->searchable()
                                ,
                                TextInput::make('harga_beli')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->default(fn ($get) => $get('menu_id') ? Menu::find($get('menu_id'))?->harga_menu ?? 0 : 0)
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    ->hidden()
                                    ->dehydrated()
                                ,
                                TextInput::make('harga_jual')
                                    ->label('Harga Menu')
                                    ->numeric()
                                    // ->reactive()
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    // ->required()
                                    ->dehydrated()
                                ,
                                TextInput::make('jml')
                                    ->label('Jumlah')
                                    ->default(1)
                                    ->reactive()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // $harga = $get('harga_jual'); // Ambil harga menu
                                        // $total = $harga * $state; // Hitung total
                                        // $set('total', $total); // Set total secara otomatis
                                        $totalTagihan = collect($get('penjualan_menu'))
                                        ->sum(fn ($item) => ($item['harga_jual'] ?? 0) * ($item['jml'] ?? 0));
                                        $set('tagihan', $totalTagihan);
                                    })
                                ,
                                DatePicker::make('tgl')
                                ->default(today()) // Nilai default: hari ini
                                ->required(),
                            ])
                            ->columns([
                                'md' => 4, //mengatur kolom menjadi 4
                            ])
                            ->addable()
                            ->deletable()
                            ->reorderable()
                            ->createItemButtonLabel('Tambah Item') // Tombol untuk menambah item baru
                            ->minItems(1) // Minimum item yang harus diisi
                            ->required() // Field repeater wajib diisi
                            ,

                            //tambahan form simpan sementara
                            // *Tombol Simpan Sementara*
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Simpan Sementara')
                                    ->action(function ($get) {
                                        $penjualan = Penjualan::updateOrCreate(
                                            ['no_faktur' => $get('no_faktur')],
                                            [
                                                'tgl' => $get('tgl'),
                                                'costumer_id' => $get('costumer_id'),
                                                'status' => 'pesan',
                                                'tagihan' => 0
                                            ]
                                        );

                                        // Simpan data menu
                                        foreach ($get('items') as $item) {
                                            PenjualanMenu::updateOrCreate(
                                                [
                                                    'penjualan_id' => $penjualan->id,
                                                    'menu_id' => $item['menu_id']
                                                ],
                                                [
                                                    'harga_beli' => $item['harga_beli'],
                                                    'harga_jual' => $item['harga_jual'],
                                                    'jml' => $item['jml'],
                                                    'tgl' => $item['tgl'],
                                                ]
                                            );

                                            // Kurangi stok menu di tabel menu
                                            $menu = Menu::find($item['menu_id']);
                                            if ($menu) {
                                                $menu->decrement('stok', $item['jml']); // Kurangi stok sesuai jumlah menu yang dibeli
                                            }
                                        }

                                        // Hitung total tagihan
                                        $totalTagihan = PenjualanMenu::where('penjualan_id', $penjualan->id)
                                            ->sum(DB::raw('harga_jual * jml'));

                                        // Update tagihan di tabel penjualan2
                                        $penjualan->update(['tagihan' => $totalTagihan]);
                                                                    })
                                        
                                        ->label('Proses')
                                        ->color('primary'),
                                                            
                                    ])    
       
                        // 
                    ])
                    ,
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                    ->content(fn (Get $get) => view('filament.components.penjualan-table', [
                                        'pembayarans' => Penjualan::where('no_faktur', $get('no_faktur'))->get()
                                ])), 
                        ]),
                ])->columnSpan(3)
                // Akhir Wizar
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                TextColumn::make('costumer.nama_costumer') // Relasi ke nama costumer
                    ->label('Nama Costumer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar' => 'success',
                        'pesan' => 'warning',
                    }),
                TextColumn::make('tagihan')
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    // ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                    ->sortable()
                    ->alignment('end') // Rata kanan
                ,
                TextColumn::make('created_at')->label('Tanggal')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesan' => 'Pemesanan',
                        'bayar' => 'Pembayaran',
                    ])
                    ->searchable()
                    ->preload(), // Menampilkan semua opsi saat filter diklik
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            // tombol tambahan
            ->headerActions([
                // tombol tambahan export pdf
                // ✅ Tombol Unduh PDF
                TableAction::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $penjualan = Penjualan::all();

                    $pdf = Pdf::loadView('pdf.penjualan', ['penjualan' => $penjualan]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'pelanggan-list.pdf'
                    );
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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