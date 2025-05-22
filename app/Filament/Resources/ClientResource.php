<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('card_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('card_id'),
                Tables\Columns\TextColumn::make('subscriptions.status')
                    ->label('Suscripción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('phone'),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Cliente')
                    ->description('Datos del cliente')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('edit')
                            ->label('Editar Cliente')
                            ->url(fn (Client $record): string => ClientResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('card_id'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('phone'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime('d-m-Y H:i')
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Suscripciones')
                    ->description('Subscripciones actuales del cliente, para ver el historial de suscripciones, haga click aquí')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('create')
                            ->label('Crear Suscripción')
                            ->url(fn (Client $record): string => ClientResource::getUrl('create', ['record' => $record])),
                    ])
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('subscriptions')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('status'),
                                Infolists\Components\TextEntry::make('start_date')
                                    ->dateTime('d-m-Y H:i'),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->dateTime('d-m-Y H:i'),
                                Infolists\Components\TextEntry::make('price')
                                    ->money('USD'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'card_id'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Carnet' => $record->card_id,
        ];
    }
}
