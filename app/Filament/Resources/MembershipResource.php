<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipResource\Pages;
use App\Filament\Resources\MembershipResource\RelationManagers;
use App\Models\Membership;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $navigationLabel = 'Membresías';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->prefix('Bs.')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('duration')
                    ->prefix('Días')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('max_installments')
                    ->label('Paga en cuotas')
                    ->hint(function ($state, Get $get): string {
                        $price = $get('price');
                        if ($price) {
                            $installments = $price / $state;
                            return 'Paga en ' . $state . ' cuotas de ' . $installments . ' Bs.';
                        }
                        return '';
                    })
                    ->live()
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Checkbox::make('has_max_checkins')
                    ->label('Maximo de checkins?')
                    ->live(),
                Forms\Components\TextInput::make('max_checkins')
                    ->label('Máx. checkins')
                    ->numeric()
                    ->required()
                    ->visible(fn (Get $get): bool => $get('has_max_checkins')),
                Forms\Components\Checkbox::make('is_promo')
                    ->label('Promo?')
                    ->live(),
                Forms\Components\DatePicker::make('promo_start_date')
                    ->label('Fecha de inicio de promo')
                    ->visible(fn (Get $get): bool => $get('is_promo')),
                Forms\Components\DatePicker::make('promo_end_date')
                    ->label('Fecha de fin de promo')
                    ->visible(fn (Get $get): bool => $get('is_promo')),
                Forms\Components\TextInput::make('max_clients')
                    ->label('Máx. clientes')
                    ->default(1)
                    ->numeric()
                    ->required()
                    ->visible(fn (Get $get): bool => $get('is_promo')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Membresía'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio Bs.'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duración días'),
                Tables\Columns\TextColumn::make('max_installments')
                    ->label('Paga en cuotas')
            ])
            ->filters([
                //
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberships::route('/'),
            'create' => Pages\CreateMembership::route('/create'),
            'edit' => Pages\EditMembership::route('/{record}/edit'),
        ];
    }
}
