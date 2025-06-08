<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\BahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_menu')
                    ->default(fn () => Menu::getNoMenu())
                    ->label('No Menu')
                    ->required()
                    ->readonly(),

                FileUpload::make('foto_menu')
                    ->directory('foto_menu')
                    ->required(),

                TextInput::make('nama_menu')
                    ->required()
                    ->placeholder('Masukkan nama menu'),

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
                    ->minValue(0)
                    ->reactive()
                    ->extraAttributes(['id' => 'harga-menu'])
                    ->placeholder('Masukkan harga menu')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('harga_menu', number_format((int) str_replace('.', '', $state), 0, ',', '.'))
                    ),


                // ✅ Tambahkan bagian ini
                Repeater::make('bahanBaku')
                    ->relationship('bahanBaku')
                    ->label('Bahan Baku Digunakan')
                    ->schema([
                        Select::make('bahan_baku_id')
                            ->label('Bahan Baku')
                            ->options(BahanBaku::all()->pluck('nama_bahan', 'id'))
                            ->required(),
                        TextInput::make('jumlah')
                            ->label('Jumlah Digunakan')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Tambah Bahan Baku')

                ,


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
                ->sortable(),
            ])
            ->filters([
                //

                TextColumn::make('no_menu')->label('Id Menu')->searchable(),

                ImageColumn::make('foto_menu')->label('Foto Menu')->size(50),

                TextColumn::make('nama_menu')->label('Nama Menu')->searchable()->sortable(),

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
                    ->extraAttributes(['class' => 'text-right'])
                    ->sortable()

                ->label('Harga Menu')
                ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                ->sortable()

                ,

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