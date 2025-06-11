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
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\Checkbox::make('active')
                    ->label('Activo?'),
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
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio Bs.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duración días'),
                Tables\Columns\TextColumn::make('is_promo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state ? 'Promoción' : 'Normal'),
            ])
            ->defaultSort('price', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Membresía')
                    ->description('Datos de la Membresía / Promoción')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('edit')
                            ->label('Editar Membresía')
                            ->url(fn (Membership $record): string => MembershipResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('is_promo')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn (string $state): string => $state ? 'Promoción' : 'Normal'),
                        Infolists\Components\TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (string $state): string => $state ? 'Activo' : 'Inactivo'),
                        Infolists\Components\TextEntry::make('price')
                            ->label('Precio Bs.')
                            ->prefix('Bs.'),
                        Infolists\Components\TextEntry::make('promo_start_date')
                            ->label('Fecha de inicio de promo')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_start_date;
                            }),
                        Infolists\Components\TextEntry::make('promo_end_date')
                            ->label('Fecha de fin de promo')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_end_date;
                            }),
                        Infolists\Components\TextEntry::make('duration')
                            ->label('Duración días'),
                        Infolists\Components\TextEntry::make('max_installments')
                            ->label('Paga en cuotas'),
                        Infolists\Components\TextEntry::make('max_checkins')
                            ->label('Máximo de entradas'),
                        Infolists\Components\TextEntry::make('max_clients')
                            ->label('Máximo de clientes'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de creación')
                            ->dateTime('d-m-Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Fecha de actualización')
                            ->dateTime('d-m-Y H:i'),
                    ])
                    ->columns(3),
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
            'view' => Pages\ViewMembership::route('/{record}'),
        ];
    }
}
