<?php

namespace App\Filament\SuperAdmin\Resources\Gyms;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\SuperAdmin\Resources\Gyms\Pages\ListGyms;
use App\Filament\SuperAdmin\Resources\Gyms\Pages\CreateGym;
use App\Filament\SuperAdmin\Resources\Gyms\Pages\EditGym;
use App\Filament\SuperAdmin\Resources\GymResource\Pages;
use App\Filament\SuperAdmin\Resources\GymResource\RelationManagers;
use App\Models\Gym;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GymResource extends Resource
{
    protected static ?string $model = Gym::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('address'),
                TextColumn::make('phone'),
                TextColumn::make('owner.name'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListGyms::route('/'),
            'create' => CreateGym::route('/create'),
            'edit' => EditGym::route('/{record}/edit'),
        ];
    }
}
