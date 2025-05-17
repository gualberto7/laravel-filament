<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Carbon\Carbon;
use App\Models\Client;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationLabel = 'Suscripciones';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos de la Suscripción')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Forms\Components\Select::make('membership_id')
                            ->relationship('membership', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $membership = \App\Models\Membership::find($state);
                                    if ($membership) {
                                        $set('end_date', now()->addDays($membership->duration)->format('Y-m-d'));
                                        $set('price', $membership->price);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('price')
                            ->prefix('Bs.')
                            ->readOnly(),
                        Forms\Components\DatePicker::make('start_date')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $membership = \App\Models\Membership::find($get('membership_id'));
                                    if ($membership) {
                                        $set('end_date', Carbon::parse($state)->addDays($membership->duration)->format('Y-m-d'));
                                    }
                                }
                            })
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->readOnly()
                        ]),
                
                Forms\Components\Section::make('Datos del Cliente')
                    ->schema([
                        Forms\Components\Select::make('clients')
                            ->relationship('clients', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('card_id')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $client = Client::create($data + [
                                    'gym_id' => auth()->user()->gym->id,
                                ]);
                                return $client->id;
                            })
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => $record->name . ' - ' . $record->card_id)
                            ->searchable(['name', 'card_id'])
                            ->multiple()
                            ->pivotData([])
                            ->required(),
                    ]),

                
                Forms\Components\Section::make('Datos de Pago')
                    ->schema([
                        Forms\Components\Repeater::make('Cuotas')
                        ->columns([
                            'sm' => 1,
                            'md' => 3,
                        ])
                        ->relationship('payments')
                        ->schema([
                            Forms\Components\TextInput::make('amount')
                                ->prefix('Bs.')
                                ->required(),
                            Forms\Components\Select::make('method')
                                ->options([
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                ])
                                ->required(),
                        ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clients.name')
                    ->searchable(['name', 'card_id']),
                Tables\Columns\TextColumn::make('membership.name'),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d-m-Y'),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
