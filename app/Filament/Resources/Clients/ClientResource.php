<?php

namespace App\Filament\Resources\Clients;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\CheckIns\CheckInResource;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Clients\Pages\ClientCheckIns;
use App\Filament\Resources\Clients\Pages\ClientSubscriptions;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use App\Filament\Traits\HasPagination;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions as SchemaActions;
use App\Models\Subscription;
use App\Enums\SubscriptionStatus;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

class ClientResource extends Resource
{
    use HasPagination;

    protected static ?string $model = Client::class;

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('card_id')
                    ->label('Nro. de carnet')
                    ->required()
                    ->maxLength(255)
                    ->rules(fn ($record) => [
                        Rule::unique('clients', 'card_id')
                            ->where('gym_id', auth()->user()->getCurrentGymId())
                            ->ignore($record?->id),
                    ])
                    ->validationMessages([
                        'unique' => 'Ya existe un cliente con este número de carnet en este gimnasio.',
                    ]),
                TextInput::make('phone')
                    ->label('Celular')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('latestSubscription.membership.name')
                    ->label('Membresía')
                    ->state(fn (Client $record): ?string => $record->latestSubscription->first()?->membership?->name)
                    ->default('—'),
                TextColumn::make('latestSubscription.status')
                    ->label('Estado')
                    ->badge()
                    ->state(fn (Client $record): ?SubscriptionStatus => $record->latestSubscription->first()?->status),
                TextColumn::make('phone')
                    ->label('Celular'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('checkin')
                    ->dispatch('addCheckin', fn ($record) => [$record->id]),
            ]);
        // ->toolbarActions([
        //     BulkActionGroup::make([
        //         DeleteBulkAction::make(),
        //     ]),
        // ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('latestSubscription.membership');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cliente')
                    ->description('Datos del cliente')
                    ->headerActions([
                        Action::make('edit')
                            ->label('Editar Cliente')
                            ->url(fn (Client $record): string => ClientResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre'),
                        TextEntry::make('card_id')
                            ->label('Nro. de Carnet'),
                        TextEntry::make('email')
                            ->label('Correo'),
                        TextEntry::make('phone')
                            ->label('Celular'),
                        TextEntry::make('created_at')
                            ->label('Creado en')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado en')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('created_by')
                            ->label('Registrado por'),
                        TextEntry::make('updated_by')
                            ->label('Actualizado por'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Suscripciones')
                    ->description('Últimas 2 suscripciones del cliente')
                    ->headerActions([
                        Action::make('ver_todas')
                            ->label('Historial de Suscripciones')
                            ->url(fn (Client $record): string => ClientResource::getUrl('subscriptions', ['record' => $record]))
                            ->color('gray'),
                        Action::make('create')
                            ->label('Crear Suscripción')
                            ->url(fn (Client $record): string => SubscriptionResource::getUrl('create', ['client_id' => $record->id])),
                    ])
                    ->schema([
                        RepeatableEntry::make('subscriptions')
                            ->label('')
                            ->state(fn (Client $record) => $record->subscriptions->sortByDesc('created_at')->take(2))
                            ->columns(2)
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge(),
                                TextEntry::make('membership.name')
                                    ->label('Membresía'),
                                TextEntry::make('start_date')
                                    ->label('Fecha Inicio')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('end_date')
                                    ->label('Fecha Fin')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('price')
                                    ->label('Monto')
                                    ->prefix('Bs. '),
                                SchemaActions::make([
                                    Action::make('ver_subscription')
                                        ->label('Ver detalle')
                                        ->icon('heroicon-o-arrow-top-right-on-square')
                                        ->url(fn (Subscription $record): string => SubscriptionResource::getUrl('view', ['record' => $record]))
                                        ->color('gray')
                                        ->size('sm'),
                                ])->columnSpanFull(),
                            ])
                            ->grid(2),
                    ])
                    ->columnSpanFull(),

                Section::make(CheckInResource::getModelLabel())
                    ->description(CheckInResource::getModelLabel().' del cliente')
                    ->headerActions([
                        Action::make('ver_todos')
                            ->label('Ver todos los '.CheckInResource::getPluralModelLabel())
                            ->url(fn (Client $record): string => ClientResource::getUrl('check-ins', ['record' => $record])),
                    ])
                    ->schema([
                        RepeatableEntry::make('checkins')
                            ->label('Últimos check-ins del cliente')
                            ->state(fn (Client $record) => $record->checkins->sortByDesc('created_at')->take(5))
                            ->table([
                                TableColumn::make('Fecha'),
                                TableColumn::make('Hora'),
                                TableColumn::make('Casillero'),
                                TableColumn::make('Registrado por'),
                            ])
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('updated_at')
                                    ->dateTime('H:i'),
                                TextEntry::make('locker_number'),
                                TextEntry::make('created_by'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
            'view' => ViewClient::route('/{record}'),
            'check-ins' => ClientCheckIns::route('/{record}/check-ins'),
            'subscriptions' => ClientSubscriptions::route('/{record}/subscriptions'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Celular' => $record->phone,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ClientResource::getUrl('view', ['record' => $record]);
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('checkin')
                ->dispatch('addCheckin', [$record->id]),
        ];
    }
}
