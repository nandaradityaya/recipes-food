<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Filament\Resources\RecipeResource\RelationManagers\TutorialsRelationManager;
use App\Models\Ingredient;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make ('name')
                ->required()
                ->maxLength(255),

                Forms\Components\FileUpload::make('thumbnail')
                ->image()
                ->required(),

                Forms\Components\Textarea::make('about')
                ->required()
                ->rows(10)
                ->cols(20),

                // repeater berarti bisa menambahkan lebih dari 1 item (add more)
                Forms\Components\Repeater::make('recipeIngredients') // ini many to many pake pivot table
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make ('ingredient_id') // pake select option yg berisi ingredient_id
                        ->relationship ('ingredient', 'name')
                        ->required(),
                    ]),

                Forms\Components\Repeater::make('photos')
                    ->relationship('photos')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                        ->required(),
                    ]),

                Forms\Components\Select::make( 'recipe_author_id') // hanya untuk melihat dan memilih penulisnya siapa, jadi hanya relasi ke table authors
                ->relationship('author', 'name') // ambil name di table author
                ->searchable()
                ->preload() // preload adalah ngeload data awal 10 atau 50 dulu
                ->required(),

                Forms\Components\Select::make('category_id') // pilih category id
                ->relationship('category', 'name') // pilih nama dari table category
                ->searchable() 
                ->preload()
                ->required(),

                Forms\Components\TextInput::make('url_video')
                ->required()
                ->maxLength (255),

                Forms\Components\FileUpload::make('url_file')
                ->downloadable()
                ->uploadingMessage('Uploading recipes...')
                ->acceptedFileTypes(['application/pdf'])
                ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                ->searchable(),

                Tables\Columns\TextColumn::make('category.name') // ambil relationship category dan ambil namanya
                ->searchable(),

                ImageColumn::make('author.photo') // ambil author lalu ambil foto
                ->circular(),

                Tables\Columns\ImageColumn::make('thumbnail'),
            ])

            // fitur filter !!!
            ->filters([
                //
                SelectFilter::make( 'recipe_author_id') // ambil recipe_author_id
                ->label('Author')
                ->relationship('author', 'name'),  // cari berdasarkan name di author

                SelectFilter::make('category_id') // ambil category idnya
                ->label('Category')
                ->relationship('category', 'name'), // cari berdasarkan name di category

                SelectFilter::make('ingredient_id')
                    ->label('Ingredient')
                    ->options (Ingredient::pluck('name', 'id')) // gunakan pluck dan ambil nama dan idnya saja
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                                $query->whereHas( 'recipe Ingredients', function ($query) use ($data) {
                                    $query->where('ingredient_id', $data['value']); // cocokin ingredient_id dengan value yg di inputkan oleh user, misal telur dengan id 1 maka munculin telur
                                });
                            }
                        }),
            ])
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
        return [
            //
            TutorialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
