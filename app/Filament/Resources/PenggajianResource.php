<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggajianResource\Pages;
use App\Models\Penggajian;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Get;
use Carbon\Carbon;
// tambahan untuk tombol unduh pdf
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

// untuk dapat menggunakan action
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Actions\Action as TableAction;


class PenggajianResource extends Resource
{
    protected static ?string $model = Penggajian::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Penggajian';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informasi Penggajian')
                        ->schema([
                            Forms\Components\Section::make('Slip Gaji')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([
                                    TextInput::make('no_slip_gaji')
                                        ->label('No Slip Gaji')
                                ->default(function () {
                                    // Menambahkan logika untuk otomatis mengisi no_penggajian
                                    $today = now()->format('Ymd');
                                    $countToday = Penggajian::whereDate('created_at', now()->toDateString())->count() + 1;
                                    return 'PGJ-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
                                })
                                 // Membuat field ini hanya dapat dilihat, tidak dapat diubah
                                ->required() //
                                        ->readonly()
                                        ->columnSpan(2),

                                    DatePicker::make('tgl')
                                        ->label('Tanggal')
                                        ->default(now()->toDateString())
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                            self::hitungTotalGaji($get, $set);
                                        })
                                        ->columnSpan(2),

                                    Select::make('pegawai_id')
                                        ->label('Pegawai')
                                        ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray())
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                            self::hitungTotalGaji($get, $set);
                                        })
                                        ->columnSpan(2),

                                    TextInput::make('total_gaji')
                                        ->label('Gaji (Otomatis dari Absensi x Gaji per Hari)')
                                        ->readonly()
                                        ->required()
                                        ->default(0)
                                        ->dehydrated() // simpan ke DB
                                        ->afterStateHydrated(function (callable $set, Get $get) {
                                            self::hitungTotalGaji($get, $set);
                                        })
                                        ->columnSpan(2),
                                ])
                                ->collapsible()
                                ->columns(3),
                        ]),

                    Wizard\Step::make('Pembayaran Gaji')
    ->schema([
        Placeholder::make('info')
            ->label('Informasi')
            ->content('Gaji dihitung otomatis berdasarkan jumlah absensi pegawai di bulan ini dikalikan gaji per hari.'),

        Select::make('status_pembayaran')
            ->label('Status Pembayaran')
            ->options([
                'dibayar' => 'dibayar',
                'belum dibayar' => 'belum dibayar',
            ])
            ->default('proses')
            ->required(),
    ]),

                        
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_slip_gaji')->label('No Slip')->searchable(),
                TextColumn::make('pegawai.nama_pegawai')->label('Nama Pegawai')->sortable()->searchable(),
                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dibayar' => 'success',
                        'belum dibayar' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('total_gaji')
                    ->label('Gaji')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignment('end'),
                TextColumn::make('created_at')->label('Tanggal')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status_pembayaran')
                    ->label('Filter Status')
                    ->options([
                        'dibayar' => 'dibayar',
                        'belum dibayar' => 'belum dibayar',
                    ])
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            
            ])

            // tombol tambahan
->headerActions([
    TableAction::make('downloadPdf')
        ->label('Unduh PDF')
        ->icon('heroicon-o-document-arrow-down')
        ->color('success')
        ->action(function () {
            $penggajian = Penggajian::with('pegawai')->get(); // dengan relasi pegawai

            $pdf = Pdf::loadView('pdf.penggajian', ['penggajian' => $penggajian]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'laporan-penggajian.pdf'
            );
        }),
])            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenggajians::route('/'),
            'create' => Pages\CreatePenggajian::route('/create'),
            'edit' => Pages\EditPenggajian::route('/{record}/edit'),
        ];
    }

    // Fungsi untuk menghitung total gaji berdasarkan absensi dan gaji per hari
    public static function hitungTotalGaji(Get $get, callable $set): void
    {
        $pegawaiId = $get('pegawai_id');
        $tanggal = $get('tgl') ?? now();

        if (!$pegawaiId) {
            $set('total_gaji', 0);
            return;
        }

        $pegawai = Pegawai::find($pegawaiId);
        if (!$pegawai) {
            $set('total_gaji', 0);
            return;
        }

        $gajiPerHari = $pegawai->gaji_per_hari ?? 0;

        $jumlahAbsensi = DB::table('absensi')
            ->where('pegawai_id', $pegawaiId)
            ->whereMonth('tgl', Carbon::parse($tanggal)->month)
            ->whereYear('tgl', Carbon::parse($tanggal)->year)
            ->where('status', 'hadir')
            ->count();

        $totalGaji = $gajiPerHari * $jumlahAbsensi;

        $set('total_gaji', $totalGaji);
    }
}
