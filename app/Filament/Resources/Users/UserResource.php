<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Actions\EditAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Toggle;
use App\Filament\Traits\HasPagination;
use Illuminate\Support\Facades\Hash;
use Filament\Actions\DeleteAction;

class UserResource extends Resource
{
    use HasPagination;

    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Usuarios';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

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
                    ->required()
                    ->validationMessages([
                        'unique' => 'El nombre de usuario ya está en uso.',
                    ]),
                TextInput::make('email')
                    ->label('Correo')
                    ->required()
                    ->email(),
                TextInput::make('phone')
                    ->label('Celular')
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
                    ->options(fn () => auth()->user()->availableRoles()->mapWithKeys(
                        fn ($role) => [$role->id => \App\Enums\RoleEnum::tryFrom($role->name)?->label() ?? $role->name]
                    ))
                    ->required()
                    ->multiple(),
                Toggle::make('is_active')
                    ->label('Activo?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('email')
                    ->label('Correo'),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->formatStateUsing(fn (string $state) => \App\Enums\RoleEnum::tryFrom($state)?->label() ?? $state),
                IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
