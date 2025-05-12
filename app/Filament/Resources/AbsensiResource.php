<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select; //untuk penggunaan select
use Filament\Forms\Components\TextInput; //untuk penggunaan text input
use Filament\Forms\Components\DateTimePicker; //untuk penggunaan date time picker
use Filament\Forms\Components\Checkbox; //untuk penggunaan checkbox
use Filament\Tables\Columns\TextColumn; //untuk tampilan tabel


class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('pegawai_id')
                        ->label('Pegawai')
                        ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray()) // Mengambil data dari tabel
                                        ->required()
                                        ->placeholder('Pilih Pegawai') // Placeholder default
                                    ,

                TextInput::make('no_absensi')
                 ->default(fn () => absensi::getNoAbsensi()) 
                        ->label('No Absensi')
                        ->required()
                        ->readonly() // Membuat field menjadi read-only
                ,
                Select::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                    ])
                    ->required(),

                    DateTimePicker::make('tgl')->default(now()) // Nilai default: waktu sekarang
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai') // Relasi ke nama pembeli
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_absensi')
                    ->label('No Absensi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),

                Tables\Columns\TextColumn::make('tgl')
                    ->label('Tanggal')
                    ->dateTime('d M Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
