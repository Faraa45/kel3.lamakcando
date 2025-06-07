<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use App\Models\User;


class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User Id')
                    ->relationship('user', 'email')
                    ->searchable() // Menambahkan fitur pencarian
                    ->preload() // Memuat opsi lebih awal untuk pengalaman yang lebih cepat
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::find($state);
                            $set('nama_pegawaii', $user->name);}}),
                            
                TextInput::make('id_pegawai')
                    ->default(fn () => Pegawai::getIdPegawai())
                    ->label('Id Pegawai')
                    ->required()
                    ->readonly(),

                TextInput::make('nama_pegawai')
                    ->required()
                    ->placeholder('Masukkan nama pegawai'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->placeholder('Masukkan email'),

                Select::make('role')
                    ->label('Jabatan')
                    ->options([
                        'Chef' => 'Chef',
                        'Kasir' => 'Kasir',
                        'Waiters' => 'Waiters',
                        'Finance' => 'Finance',
                    ])
                    ->searchable()
                    ->required(),

                TextInput::make('no_telepon')
                    ->required()
                    ->placeholder('Masukkan no telepon'),

                TextInput::make('no_rekening')

                    ->required()
                    ->label('No Rekening')
                    ->placeholder('Masukkan nomor rekening')
                    ->numeric() // Validasi hanya menerima angka
                    ->maxLength(50), // Membatasi panjang nomor rekening sesuai kebutuhan

                // Input untuk Gaji per Hari
                TextInput::make('gaji_per_hari')
                    ->label('Gaji per Hari')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('Masukkan gaji per hari'),

                    ->label('No Rekening')
                    ->numeric()
                    ->placeholder('Masukkan no rekening')
                    ->required(),

                Select::make('bank')
                    ->label('Bank')
                    ->options([
                        'BCA' => 'BCA',
                        'BRI' => 'BRI',
                        'BNI' => 'BNI',
                        'Mandiri' => 'Mandiri',
                        'CIMB' => 'CIMB',
                        'Danamon' => 'Danamon',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->searchable()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pegawai')->label('ID Pegawai'),
                TextColumn::make('nama_pegawai')->label('Nama Pegawai'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('role')->label('Role'),
                TextColumn::make('no_telepon')->label('No Telepon'),
                TextColumn::make('no_rekening')->label('No Rekening'),

                
                // Format Gaji per Hari tanpa prefix Rp di table
                TextColumn::make('gaji_per_hari')
                    ->label('Gaji per Hari')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),

                TextColumn::make('bank')->label('Bank'),

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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}