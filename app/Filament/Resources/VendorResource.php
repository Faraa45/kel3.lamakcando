<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn; // Perbaikan!

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Data Vendor';
    
    protected static ?string $modelLabel = 'Vendor';
    
    protected static ?string $pluralModelLabel = 'Vendors';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nomor_vendor')
                ->default(fn () => Vendor::getNomorVendor()) // Perbaikan!
                ->label('Nomor Vendor')
                ->required()
                ->readonly(), 

            TextInput::make('nama_vendor')
                ->label('Nama Vendor')
                ->required()
                ->maxLength(255),
            
            Select::make('status')
                ->label('Status Vendor')
                ->options([
                    'tersedia' => 'Tersedia',
                    'tidak tersedia' => 'Tidak Tersedia'
                ])
                ->required(),
            
            Select::make('keterangan')
                ->label('Jenis Produk')
                ->options([
                    'makanan' => 'Makanan',
                    'minuman' => 'Minuman'
                ])
                ->required(),
            
            TextInput::make('tipe')
                ->label('Tipe Vendor')
                ->required()
                ->maxLength(255),
            
            Textarea::make('alamat')
                ->label('Alamat Lengkap')
                ->required()
                ->rows(3),
            
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_vendor') // Perbaikan!
                ->sortable(),
                TextColumn::make('nama_vendor')
                    ->label('Nama Vendor')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'tidak tersedia' => 'danger',
                    }),
                
                TextColumn::make('keterangan')
                    ->label('Jenis Produk'),
                
                TextColumn::make('tipe')
                    ->label('Tipe Vendor'),
                
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30),
                
                TextColumn::make('email')
                    ->label('Email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
