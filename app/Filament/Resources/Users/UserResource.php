<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;
use App\Filament\Traits\HasPagination;

class UserResource extends Resource
{
    use HasPagination;

    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Usuarios';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 2;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('username')
                    ->label('Usuario')
                    ->unique()
                    ->live()
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->required(),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->hiddenOn(['edit'])
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->required(),
                Select::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->required()
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.name'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles')->where('gym_id', auth()->user()->getCurrentGymId());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
