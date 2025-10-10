<?php

namespace App\Filament\Resources\Memberships;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Resources\Memberships\Pages\EditMembership;
use App\Filament\Resources\Memberships\Pages\ViewMembership;
use App\Filament\Resources\MembershipResource\Pages;
use App\Models\Membership;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Illuminate\Database\Eloquent\Model;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $navigationLabel = 'Membresías';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 3;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->prefix('Bs.')
                    ->numeric()
                    ->required(),
                TextInput::make('duration')
                    ->prefix('Días')
                    ->required()
                    ->numeric(),
                Checkbox::make('active')
                    ->label('Activo?'),
                TextInput::make('max_installments')
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
                Checkbox::make('has_max_checkins')
                    ->label('Maximo de checkins?')
                    ->live(),
                TextInput::make('max_checkins')
                    ->label('Máx. checkins')
                    ->numeric()
                    ->required()
                    ->visible(fn (Get $get): bool => $get('has_max_checkins')),
                Checkbox::make('is_promo')
                    ->label('Promo?')
                    ->live(),
                DatePicker::make('promo_start_date')
                    ->label('Fecha de inicio de promo')
                    ->visible(fn (Get $get): bool => $get('is_promo')),
                DatePicker::make('promo_end_date')
                    ->label('Fecha de fin de promo')
                    ->visible(fn (Get $get): bool => $get('is_promo')),
                TextInput::make('max_clients')
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
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('price')
                    ->label('Precio Bs.')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duración días'),
                TextColumn::make('is_promo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state ? 'Promoción' : 'Normal'),
            ])
            ->defaultSort('price', 'asc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Membresía')
                    ->description('Datos de la Membresía / Promoción')
                    ->headerActions([
                        Action::make('edit')
                            ->label('Editar Membresía')
                            ->url(fn (Membership $record): string => MembershipResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('is_promo')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn (string $state): string => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn (string $state): string => $state ? 'Promoción' : 'Normal'),
                        TextEntry::make('is_active')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (string $state): string => $state ? 'Activo' : 'Inactivo'),
                        TextEntry::make('price')
                            ->label('Precio Bs.')
                            ->prefix('Bs.'),
                        TextEntry::make('promo_start_date')
                            ->label('Fecha de inicio de promo')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_start_date;
                            }),
                        TextEntry::make('promo_end_date')
                            ->label('Fecha de fin de promo')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_end_date;
                            }),
                        TextEntry::make('duration')
                            ->label('Duración días'),
                        TextEntry::make('max_installments')
                            ->label('Paga en cuotas'),
                        TextEntry::make('max_checkins')
                            ->label('Máximo de entradas'),
                        TextEntry::make('max_clients')
                            ->label('Máximo de clientes'),
                        TextEntry::make('created_at')
                            ->label('Fecha de creación')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('updated_at')
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
            'index' => ListMemberships::route('/'),
            'create' => CreateMembership::route('/create'),
            'edit' => EditMembership::route('/{record}/edit'),
            'view' => ViewMembership::route('/{record}'),
        ];
    }
}
