<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput; //kita menggunakan textinput
use Filament\Forms\Components\Grid;

use Filament\Tables\Columns\TextColumn;

use App\Filament\Resources\CoaResource\Pages;
use App\Filament\Resources\CoaResource\RelationManagers;
use App\Models\Coa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class CoaResource extends Resource
{
    protected static ?string $model = Coa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // tambahan buat label Jurnal Umum
    protected static ?string $navigationLabel = 'COA';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Master Data';
    public static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make(1) // Membuat hanya 1 kolom
            ->schema([
                TextInput::make('header_akun')
                    ->required()
                    ->placeholder('Masukkan header akun')
                ,
                TextInput::make('kode_akun')
                    ->required()
                    ->placeholder('Masukkan kode akun')
                ,
                TextInput::make('nama_akun')
                    ->autocapitalize('words')
                    ->label('Nama akun')
                    ->required()
                    ->placeholder('Masukkan nama akun')
                ,
            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('header_akun'),
            TextColumn::make('kode_akun'),
            TextColumn::make('nama_akun'), 
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('header_akun')
                ->options([
                    1 => 'Aset/Aktiva',
                    2 => 'Kewajiban',
                    3 => 'Ekuitas',
                    4 => 'Pendapatan',
                    5 => 'Beban',
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
            'index' => Pages\ListCoas::route('/'),
            'create' => Pages\CreateCoa::route('/create'),
            'edit' => Pages\EditCoa::route('/{record}/edit'),
        ];
    }
}
