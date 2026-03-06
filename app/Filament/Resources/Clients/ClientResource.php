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
use Filament\Schemas\Components\Livewire;
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
use App\Livewire\CheckIn\Index;
use App\Filament\Traits\HasPagination;
use Filament\Actions\Action;

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
                    ->required()
                    ->maxLength(255),
                TextInput::make('card_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
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
                    ->state(fn (Client $record): ?string => $record->latestSubscription->first()?->status)
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'expires_soon' => 'warning',
                        'expires_today' => 'danger',
                        'expired', 'inactive' => 'gray',
                        default => 'gray',
                    }),
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
                        \Filament\Actions\Action::make('edit')
                            ->label('Editar Cliente')
                            ->url(fn (Client $record): string => ClientResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('card_id'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('created_at')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('updated_at')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('created_by'),
                        TextEntry::make('updated_by'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Suscripciones')
                    ->description('Últimas 4 suscripciones del cliente')
                    ->headerActions([
                        \Filament\Actions\Action::make('ver_todas')
                            ->label('Ver todas')
                            ->url(fn (Client $record): string => ClientResource::getUrl('subscriptions', ['record' => $record]))
                            ->color('gray'),
                        \Filament\Actions\Action::make('create')
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
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'expires_soon' => 'warning',
                                        'expires_today' => 'danger',
                                        'expired' => 'gray',
                                    }),
                                TextEntry::make('membership.name')
                                    ->label('Membresía'),
                                TextEntry::make('start_date')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('end_date')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('price')
                                    ->prefix('Bs. '),
                            ])
                            ->grid(2),
                    ])
                    ->columnSpanFull(),

                Section::make('Check-in')
                    ->description('Check-in del cliente')
                    ->headerActions([
                        Action::make('ver_todos')
                            ->label('Ver todos los check-in')
                            ->url(fn (Client $record): string => ClientResource::getUrl('check-ins', ['record' => $record])),
                    ])
                    ->schema([
                        Livewire::make(Index::class),
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
