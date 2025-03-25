<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
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
use Filament\Forms\Components\FileUpload; //untuk tipe file

use Filament\Tables\Columns\BadgeColumn;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;


class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_menu')
                    ->default(fn () => Menu::getNoMenu()) // Ambil default dari method getKodeBarang
                    ->label('No Menu')
                    ->required()
                    ->readonly() // Membuat field menjadi read-only
                ,

                FileUpload::make('foto_menu')
                ->label('Foto')
                ->image()
                ->directory('images')
                ->required(),

                TextInput::make('nama_menu')
                    ->required()
                    ->placeholder('Masukkan nama menu') // Placeholder untuk membantu pengguna
                ,
                
                Select::make('kategori_menu')
                ->label('Kategori')
                ->options([
                    'Makanan' => 'Makanan',
                    'Minuman' => 'Minuman',
                    'Camilan' => 'Camilan',
                ])
                ->required(),

                TextInput::make('harga_menu')
                    ->required()
                    ->minValue(0) // Nilai minimal 0 (opsional jika tidak ingin ada harga negatif)
                    ->reactive() // Menjadikan input reaktif terhadap perubahan
                    ->extraAttributes(['id' => 'harga-menu']) // Tambahkan ID untuk pengikatan JavaScript
                    ->placeholder('Masukkan harga menu') // Placeholder untuk membantu pengguna
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('harga_menu', number_format((int) str_replace('.', '', $state), 0, ',', '.'))
                      )
                , // nokmr, nm kmr, lntai kmr, foto, hrga kmar, status kmr
               

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_menu')
                ->label('Id Menu')
                ->searchable(),

                ImageColumn::make('foto_menu') // Menampilkan gambar di tabel
                ->label('Foto Menu')
                ->size(50), // Menyesuaikan ukuran thumbnail
                

                TextColumn::make('nama_menu')
                ->label('Nama Menu')
                ->searchable()
                ->sortable(),

                BadgeColumn::make('kategori_menu')
                ->label('Kategori')
                ->color(fn (string $state): string => match ($state) {
                    'Makanan' => 'primary',
                    'Minuman' => 'success',
                    'Camilan' => 'info',
                }),

                TextColumn::make('harga_menu')
                ->label('Harga Menu')
                ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                ->sortable()
                ,

            ])
            ->filters([
                //
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
