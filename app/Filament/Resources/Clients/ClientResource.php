<?php

namespace App\Filament\Resources\Clients;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Livewire;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Livewire\CheckIn\Index;
use App\Filament\Traits\HasPagination;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    use HasPagination;

    protected static ?string $model = Client::class;

    protected static ?string $navigationLabel = 'Clientes';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('card_id'),
                TextColumn::make('subscriptions.status')
                    ->label('Suscripción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('phone'),
            ])
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
                    ->columns(3),

                Section::make('Suscripciones')
                    ->description('Subscripciones actuales del cliente, para ver el historial de suscripciones, haga click aquí')
                    ->headerActions([
                        \Filament\Actions\Action::make('create')
                            ->label('Crear Suscripción')
                            ->url(fn (Client $record): string => ClientResource::getUrl('create', ['record' => $record])),
                    ])
                    ->schema([
                        RepeatableEntry::make('subscriptions')
                            ->label('')
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
                                TextEntry::make('start_date')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('end_date')
                                    ->dateTime('d-m-Y'),
                                TextEntry::make('price')
                                    ->prefix('Bs. '),
                            ])
                            ->grid(2),
                    ]),

                Section::make('Check-in')
                    ->description('Check-in del cliente')
                    ->headerActions([
                        \Filament\Actions\Action::make('create')
                            ->label('Ver todos los check-in')
                            ->url(fn (Client $record): string => ClientResource::getUrl('view', ['record' => $record])),
                    ])
                    ->schema([
                        Livewire::make(Index::class)
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
            'view' => ViewClient::route('/{record}'),
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
                ->dispatch('addCheckin', [$record->id])
        ];
    }
}
