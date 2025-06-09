<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Filament\Resources\PembelianResource\RelationManagers;
use App\Models\Pembelian;
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
//use App\Models\Costumer;
use App\Models\BahanBaku;
//use App\Models\Pembayaran;
use App\Models\PembelianBahanBaku;
use App\Models\Vendor;

// DB
use Illuminate\Support\Facades\DB;
// untuk dapat menggunakan action
//use Filament\Forms\Components\Actions\Action;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

        // merubah nama label menjadi Pembeli
    protected static ?string $navigationLabel = 'Pembelian';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Transaksi';
    public static ?int $navigationSort = 2;

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
                                // ->description('Detail Barang')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([ 
                                    TextInput::make('no_faktur_pembelian')
                                        ->default(fn () => Pembelian::getKodeFaktur()) // Ambil default dari method getKodeBarang
                                        ->label('Nomor Faktur Pembelian')
                                        ->required()
                                        ->readonly() // Membuat field menjadi read-only
                                    ,
                                    DateTimePicker::make('tanggal')->default(now()) // Nilai default: waktu sekarang
                                    ,
                                    Select::make('vendor_id')
                                        ->label('Vendor')
                                        ->options(Vendor::pluck('nama_vendor', 'id')->toArray()) // Mengambil data dari tabel
                                        ->required()
                                        ->placeholder('Pilih Vendor') // Placeholder default
                                    ,
                                   
                                    TextInput::make('total_tagihan')
                                        ->default('0') // Nilai default status pemesanan adalah pesan/bayar/kirim
                                        ->hidden()
                                    ,
                                
                                ])
                                ->collapsible() // Membuat section dapat di-collapse
                                ->columns(3)
                            ,
                        ]),
                    Wizard\Step::make('Pilih Barang')
                    ->schema([
                        Hidden::make('pembelian_id'),
                            // untuk menambahkan repeater
                            Repeater::make('items')
                            ->relationship('pembelianBahanBaku')
                            // ->live()
                            ->schema([
                                Select::make('bahan_baku_id')
                                        ->label('Bahan Baku')
                                        ->options(BahanBaku::pluck('nama_bahan', 'id')->toArray())
                                        // Mengambil data dari tabel
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems() //agar komponen item tidak berulang
                                        ->reactive() // Membuat field reactive
                                        ->placeholder('Pilih Barang') // Placeholder default
                                        ->afterStateUpdated(function ($state, $set) {
                                            $bahan_baku = BahanBaku::find($state);
                                            $set('harga_beli', $bahan_baku ? $bahan_baku->harga_satuan : 0);
                                        })
                                ,
                                TextInput::make('harga_beli')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->default(fn ($get) => BahanBaku::find($get('bahan_baku_id')) ? BahanBaku::find($get('bahan_baku_id'))->harga_satuan : 0)
                                    ->readonly() // Agar pengguna tidak bisa mengedit
                                    //->hidden()
                                    ->dehydrated()
                                ,
                                TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->default(1)
                                    ->reactive()
                                    ->live()
                                    ->required()
                                     ->afterStateUpdated(function (Set $set, Get $get) {
                                    $items = $get('items') ?? [];
                                    $total = 0;
                                    foreach ($items as $item) {
                                        $total += ($item['harga_beli'] ?? 0) * ($item['jumlah'] ?? 0);
                                    }
                                    $set('total_tagihan', $total);
                                                })
                                ,
                                DatePicker::make('tanggal')
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
                            // **Tombol Simpan Sementara**
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Simpan Sementara')
                                    ->action(function ($get, $set) {
                                         \Log::info('Data Pembelian: ', [
                                        $pembelian = Pembelian::updateOrCreate(
                                            ['no_faktur_pembelian' => $get('no_faktur_pembelian')],
                                            [
                                                'tanggal' => $get('tanggal'),
                                                'vendor_id' => $get('vendor_id'),
                                                'status' => 'pesan',
                                                'total_tagihan' => 0
                                            ])
                                        
                                         ]);

                                        // Simpan data barang
                                        foreach ($get('items') as $item) {
                                            PembelianBahanBaku::updateOrCreate(
                                                [
                                                    'pembelian_id' => $pembelian->id,
                                                    'bahan_baku_id' => $item['bahan_baku_id']
                                                ],
                                                [
                                                    'harga_beli' => $item['harga_beli'],
                                                    'jumlah' => $item['jumlah'],
                                                    'tanggal' => $item['tanggal'],
                                                ]
                                            );

                                            // nambah stok barang di tabel barang
                                            $bahan_baku = BahanBaku::find($item['bahan_baku_id']);
                                            if ($bahan_baku) {
                                                $bahan_baku->increment('stok', $item['jumlah']); // nambah stok sesuai jumlah barang yang dibeli
                                            }
                                        }

                                        // Hitung total tagihan
                                        $totalTagihan = PembelianBahanBaku::where('pembelian_id', $pembelian->id)
                                            ->sum(DB::raw('harga_beli * jumlah'));

                                        // Update tagihan di tabel Pembelian2
                                        $pembelian->update(['total_tagihan' => $totalTagihan]);

                                            // 🔥 SIMPAN PEMBELIAN ID KE FORM STATE 🔥
                                        $set('pembelian_id', $pembelian->id);
                                        })
                                        
                                        ->label('Proses')
                                        ->color('primary'),
                                                            
                                    ])    
       
                        // 
                    ])
                    ,
                    Wizard\Step::make('Detail Pembelian')
                        ->schema([
                            Placeholder::make('Tabel Pembelian')
                                    ->content(function (Get $get) {
                                        $pembelian = Pembelian::where('no_faktur_pembelian', $get('no_faktur_pembelian'))->first();
                                        if (!$pembelian) return "Belum ada data pembelian.";

                                        return view('filament.components.pembelian-table', [
                                            'pembelian' => $pembelian,
                                            'items' => PembelianBahanBaku::where('pembelian_id', $pembelian->id)->get(),
                                        ]);
                                        }),
                                ]),
                ])->columnSpan(3)
                // Akhir Wizard
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur_pembelian')->label('No Faktur')->searchable(),
                TextColumn::make('vendor.nama_vendor') // Relasi ke nama vendor
                    ->label('Nama Vendor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_tagihan') ->label('Tagihan')
                    ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    // ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                    ->sortable()
                    ->alignment('end') // Rata kanan
                ,
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pesan' => 'success',
                        'diterima' => 'warning',
                        'selesai' => 'gray',
            })  
                    ->default('pesan')
                    ->label('Status Pembelian'),
                    //->hidden(), // tampilkan hanya jika kamu butuh ganti status manual

                TextColumn::make('created_at')->label('Tanggal')->dateTime(),
            ])
             ->filters([
                SelectFilter::make('vendor_id')
                    ->label('Filter vendor')
                    ->options(Vendor::pluck('nama_vendor', 'id')),
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesan' => 'Dipesan',
                        'diterima' => 'Diterima',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}