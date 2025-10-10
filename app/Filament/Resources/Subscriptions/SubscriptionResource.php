<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Carbon\Carbon;
use App\Models\Client;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Filament\Traits\HasPagination;

class SubscriptionResource extends Resource
{
    use HasPagination;

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
                            ->relationship('membership', 'name', function ($query) {
                                \App\Models\Membership::getActivePromosQuery($query);
                            })
                            ->required()
                            ->live()
                            ->disabledOn(['edit'])
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                if ($state) {
                                    $membership = \App\Models\Membership::find($state);
                                    $installments = $get('installments');
                                    if ($membership) {
                                        $set('end_date', now()->addDays($membership->duration)->format('Y-m-d'));
                                        $set('price', $membership->price);
                                        
                                        $firstInstallmentKey = array_key_first($installments);
                                        if ($firstInstallmentKey) {
                                            $installments[$firstInstallmentKey]['amount'] = $membership->price / $membership->max_installments;
                                            $set('installments', $installments);
                                        }
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('price')
                            ->prefix('Bs.')
                            ->readOnly(),
                        Forms\Components\DatePicker::make('start_date')
                            ->default(now())
                            ->live()
                            ->disabledOn(['edit'])
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
                            ->createOptionUsing(function (array $data): string {
                                $client = Client::create($data + [
                                    'gym_id' => auth()->user()->getCurrentGymId(),
                                ]);
                                return $client->id;
                            })
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => $record->name . ' - ' . $record->card_id)
                            ->searchable(['name', 'card_id'])
                            ->multiple()
                            ->required()
                            ->disabledOn(['edit'])
                            ->minItems(1),
                    ]),
                
                Forms\Components\Section::make('Datos de Pago')
                    ->schema([
                        Forms\Components\Repeater::make('installments')
                            ->columns([
                                'sm' => 1,
                                'md' => 3,
                            ])
                            ->minItems(1)
                            ->maxItems(function (Get $get): int {
                                $membership = \App\Models\Membership::find($get('membership_id'));
                                return $membership->max_installments ?? 1;
                            })
                            ->relationship('payments')
                            ->label(fn (Get $get): string => self::updatePaymentStatus($get))
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->prefix('Bs.')
                                    ->live()
                                    ->required(),
                                Forms\Components\Select::make('method')
                                    ->options([
                                        'cash' => 'Efectivo',
                                        'card' => 'Tarjeta',
                                    ])
                                    ->required(),
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->default(now())
                                    ->readOnly()
                                    ->required(),
                            ])
                            ->cloneable()
                            ->addActionLabel('Agregar Pago'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->groups([
                'membership.name'
            ])
            ->columns([
                Tables\Columns\TextColumn::make('clients.name')
                    ->searchable(['name', 'card_id']),
                Tables\Columns\TextColumn::make('membership.name'),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime('d-m-Y'),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'expires_soon' => 'warning',
                    'expires_today' => 'danger',
                    'expired' => 'gray',
                })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership.name')
                    ->relationship('membership', 'name')
                    ->label('Membresia'),
            ])
            ->actions([
                /* Tables\Actions\Action::make('checkIn')
                    ->label('Check-in')
                    ->fillForm(fn (Subscription $record) => [
                        'client_id' => $record->clients->pluck('id'),
                    ])
                    ->form([
                        Forms\Components\TextInput::make('locker_number')
                            ->label('Caja')
                            ->required(),
                        Forms\Components\CheckboxList::make('client_id')
                            ->options(fn (Subscription $record) => $record->clients->pluck('name', 'id'))
                            ->columns(2)
                            ->required(),
                    ])
                    ->action(fn (Subscription $record, array $data) => [
                        $record->clients->each(function (Client $client) use ($data, $record) {
                            if (in_array($client->id, $data['client_id'])) {
                                $client->addCheckIn($data['locker_number']);
                            }
                        }),
                        Notification::make()
                            ->title('Check-in realizado correctamente')
                            ->success()
                            ->send(),
                    ])
                    ->modalWidth('md'), */

                Tables\Actions\ViewAction::make(),
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
                Infolists\Components\Section::make('Datos de la Suscripción')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('edit')
                            ->label('Editar')
                            ->url(fn (Subscription $record): string => SubscriptionResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Infolists\Components\TextEntry::make('clients.name'),
                        Infolists\Components\TextEntry::make('membership.name'),
                        Infolists\Components\TextEntry::make('total_paid')
                            ->label('Total Pagado')
                            ->prefix('Bs. ')
                            ->color(fn (string $state, $record): string => $state >= $record->price ? 'success' : 'warning')
                            ->hint(fn (string $state, $record): string => $state >= $record->price ? 'Pago Completo' : 'Pago Pendiente'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'expires_soon' => 'warning',
                                'expires_today' => 'danger',
                                'expired' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('start_date')
                            ->dateTime('d-m-Y'),
                        Infolists\Components\TextEntry::make('end_date')
                            ->dateTime('d-m-Y'),
                        Infolists\Components\TextEntry::make('price'),
                        Infolists\Components\TextEntry::make('created_by'),
                        Infolists\Components\TextEntry::make('updated_by'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Pagos')
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('addPayment')
                            ->label('Agregar Pago')
                            ->form([
                                Forms\Components\TextInput::make('amount')
                                    ->prefix('Bs.')
                                    ->required(),
                                Forms\Components\Select::make('method')
                                    ->options([
                                        'cash' => 'Efectivo',
                                        'card' => 'Tarjeta',
                                    ])
                                    ->required(),
                            ])
                            ->action(function (array $data, Subscription $record) {
                                $record->payments()->create($data);
                                Notification::make()
                                    ->title('Pago agregado correctamente')
                                    ->success()
                                    ->send();
                            })
                            ->modalWidth('md')
                    ])
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('payments')
                            ->label('')
                            ->columns(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('amount'),
                                Infolists\Components\TextEntry::make('method'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime('d-m-Y H:i'),
                                Infolists\Components\TextEntry::make('created_by'),
                            ]),
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
            'view' => Pages\ViewSubscription::route('/{record}'),
        ];
    }

    public static function updatePaymentStatus($get)
    {
        $membership = \App\Models\Membership::find($get('membership_id'));
        $installments = $get('installments');
        $totalAmount = array_sum(array_map(function ($installment) {
            return $installment['amount'];
        }, $installments)) ?? 0;
        if ($membership && $totalAmount) {
            return "Pagado: " . $totalAmount . " de " . $membership->price;
        }
        return '';
    }
}
