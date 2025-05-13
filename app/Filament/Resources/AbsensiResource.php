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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;

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
                    ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray())
                    ->required()
                    ->placeholder('Pilih Pegawai'),

                TextInput::make('no_absensi')
                    ->default(fn () => Absensi::getNoAbsensi())
                    ->label('No Absensi')
                    ->required()
                    ->readonly(),

                Select::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                    ])
                    ->required(),

                DatePicker::make('tgl')
                    ->label('Tanggal')
                    ->default(now()),

                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Isi keterangan jika diperlukan')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('no_absensi')
                    ->label('No Absensi')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status'),

                TextColumn::make('tgl')
                    ->label('Tanggal')
                    ->dateTime('d M Y'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(150)
                    ->wrap(),
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
