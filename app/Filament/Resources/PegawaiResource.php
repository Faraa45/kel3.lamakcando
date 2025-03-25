<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id_pegawai')
                    ->default(fn () => Pegawai::getIdPegawai())
                    ->label('Id Pegawai')
                    ->required()
                    ->readonly(),

                TextInput::make('nama_pegawai')
                    ->required()
                    ->placeholder('Masukkan nama pegawai'),

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
                    ->placeholder('Masukkan no telepon')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_pegawai')->label('ID Pegawai'),
                TextColumn::make('nama_pegawai')->label('Nama Pegawai'),
                TextColumn::make('role')->label('Role'),
                TextColumn::make('no_telepon')->label('No Telepon'),
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

