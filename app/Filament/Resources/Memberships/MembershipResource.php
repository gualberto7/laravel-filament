<?php

namespace App\Filament\Resources\Memberships;

use Closure;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Resources\Memberships\Pages\EditMembership;
use App\Filament\Resources\Memberships\Pages\ViewMembership;
use App\Models\Membership;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $modelLabel = 'Membresía';

    protected static ?string $pluralModelLabel = 'Membresías';

    protected static ?string $navigationLabel = 'Membresías';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes([
                        'data-test' => 'name-input',
                    ]),
                TextInput::make('price')
                    ->label('Precio')
                    ->prefix('Bs.')
                    ->numeric()
                    ->required()
                    ->extraInputAttributes([
                        'data-test' => 'price-input',
                    ]),
                TextInput::make('duration')
                    ->label('Tiempo (días)')
                    ->prefix('Días')
                    ->required()
                    ->numeric()
                    ->extraInputAttributes([
                        'data-test' => 'duration-input',
                    ]),
                Toggle::make('active')
                    ->label('Activo?')
                    ->inline(false)
                    ->default(true),
                TextInput::make('max_installments')
                    ->label('Paga en cuotas')
                    ->hint(function ($state, Get $get): string {
                        $price = $get('price');
                        if ($price && $state > 0) {
                            $installments = $price / $state;

                            return 'Paga en '.$state.' cuotas de '.$installments.' Bs.';
                        }

                        return '';
                    })
                    ->live()
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            if ($value < 0) {
                                return $fail('La cantidad de coutas debe ser al menos 1');
                            }
                        },
                    ])
                    ->extraInputAttributes([
                        'data-test' => 'max_installments-input',
                    ]),
                TextInput::make('max_checkins')
                    ->label('Máximo checkins (opcional)')
                    ->numeric()
                    ->hint('Cantidad de ingresos al gimnasio')
                    ->extraInputAttributes([
                        'data-test' => 'max_checkins-input',
                    ]),
                Toggle::make('is_promo')
                    ->label('Es promoción?')
                    ->live()
                    ->inline(false),
                DatePicker::make('promo_start_date')
                    ->label('Fecha Inicio')
                    ->visible(fn (Get $get): bool => $get('is_promo'))
                    ->required()
                    ->extraInputAttributes([
                        'data-test' => 'promo_start_date-input',
                    ]),
                DatePicker::make('promo_end_date')
                    ->label('Fecha Fin')
                    ->visible(fn (Get $get): bool => $get('is_promo'))
                    ->required()
                    ->extraInputAttributes([
                        'data-test' => 'promo_end_date-input',
                    ]),
                TextInput::make('max_clients')
                    ->label('Máximo de clientes')
                    ->default(1)
                    ->numeric()
                    ->required()
                    ->visible(fn (Get $get): bool => $get('is_promo'))
                    ->extraInputAttributes([
                        'data-test' => 'max_clients-input',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('price', 'acs')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre'),
                TextColumn::make('price')
                    ->label('Precio Bs.')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Tiempo (días)'),
                TextColumn::make('is_promo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state ? 'Promoción' : 'Normal'),
                IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean(),
            ])
            ->defaultSort('price', 'asc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                        DeleteAction::make()
                            ->label('Eliminar')
                            ->visible(auth()->user()->hasRole('owner'))
                            ->successRedirectUrl(MembershipResource::getUrl('index')),
                    ])
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre'),
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
                            ->label('Fecha Inicio')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_start_date;
                            }),
                        TextEntry::make('promo_end_date')
                            ->label('Fecha Fin')
                            ->dateTime('d-m-Y')
                            ->visible(function (Model $record): bool {
                                return $record->is_promo && $record->promo_end_date;
                            }),
                        TextEntry::make('duration')
                            ->label('Duración (días)'),
                        TextEntry::make('max_installments')
                            ->label('Paga en cuotas'),
                        TextEntry::make('max_checkins')
                            ->label('Máximo de check-ins'),
                        TextEntry::make('max_clients')
                            ->label('Máximo de clientes'),
                        TextEntry::make('created_at')
                            ->label('Creado en')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado en')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('created_by')
                            ->label('Creado por'),
                        TextEntry::make('updated_by')
                            ->label('Actualizado por'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
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
