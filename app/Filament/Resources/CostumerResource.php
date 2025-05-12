<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostumerResource\Pages;
use App\Filament\Resources\CostumerResource\RelationManagers;
use App\Models\Costumer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

// use Filament\Forms\Components\InputMask;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextArea;

//untuk model ke user
use App\Models\User;

class CostumerResource extends Resource
{
    protected static ?string $model = Costumer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //merubah nama label
    protected static ?string $navigationLabel = 'Costumer';

    //tambahan buat grup masterdata
    protected static ?string $group = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                //direlasikan ke tabel user
                Select::make('user_id')
                    ->label('User Id')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload() //utk memuat opsi lebih awal utk pengalaman yang lebih cepat
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Mengambil data user berdasarkan id
                            $user = User::find($state);
                        if ($user) {
                            
                            // Mengatur nilai email ke field nama
                            $set('nama_costumer', $user->name);
                        }
                    })
                    ,
                    TextInput::make('kode_costumer')
                    ->default(fn () => costumer::getKodeCostumer()) 
                    ->label('kode costumer')
                    ->required()
                    ->readonly() // Membuat field menjadi read-only
                    ,
                    TextInput::make('nama_costumer')
                        ->autocapitalize('words')
                        ->label('Nama costumer')
                        ->required()
                        ->placeholder('Masukkan nama costumer')
                        ->readonly()
                    ,
                    TextArea::make('alamat_costumer')
                        ->label('alamat costumer')
                        ->maxlength(500)
                        ->required()
                    ,
                    TextInput::make('no_telp_costumer')
                    ->autocapitalize('words')
                    ->label('No_Telp')
                    ->required()
                    ->placeholder('Masukkan No Telepon')
                    ->numeric()
                    ->prefix('+62')
                    ->extraAttributes(['pattern' => '^[0-9]+$', 'title' => 'Masukkan angka yang diawali dengan 0'])
                    ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_costumer')
                ->searchable(),

                TextColumn::make('nama_costumer')
                ->label('Nama')
                ->searchable()
                ->sortable(),
                
                TextColumn::make('alamat_costumer')
                ->label('alamat')
                ->sortable(),

                TextColumn::make('no_telp_costumer')
                ->sortable()
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListCostumers::route('/'),
            'create' => Pages\CreateCostumer::route('/create'),
            'edit' => Pages\EditCostumer::route('/{record}/edit'),
        ];
    }
}
