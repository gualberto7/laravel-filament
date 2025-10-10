<?php

namespace App\Filament\Resources\Subscriptions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use App\Models\Membership;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\CreateSubscription;
use App\Filament\Resources\Subscriptions\Pages\EditSubscription;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscription;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use App\Models\Client;
use Filament\Notifications\Notification;
use Filament\Infolists;
use App\Filament\Traits\HasPagination;

class SubscriptionResource extends Resource
{
    use HasPagination;

    protected static ?string $model = Subscription::class;

    protected static ?string $navigationLabel = 'Suscripciones';
    protected static ?int $navigationSort = 1;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Suscripción')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('membership_id')
                            ->relationship('membership', 'name', function ($query) {
                                Membership::getActivePromosQuery($query);
                            })
                            ->required()
                            ->live()
                            ->disabledOn(['edit'])
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                if ($state) {
                                    $membership = Membership::find($state);
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
                        TextInput::make('price')
                            ->prefix('Bs.')
                            ->readOnly(),
                        DatePicker::make('start_date')
                            ->default(now())
                            ->live()
                            ->disabledOn(['edit'])
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $membership = Membership::find($get('membership_id'));
                                    if ($membership) {
                                        $set('end_date', Carbon::parse($state)->addDays($membership->duration)->format('Y-m-d'));
                                    }
                                }
                            })
                            ->required(),
                        DatePicker::make('end_date')
                            ->readOnly()
                        ]),
                
                Section::make('Datos del Cliente')
                    ->schema([
                        Select::make('clients')
                            ->relationship('clients', 'name')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('card_id')
                                    ->required(),
                                TextInput::make('phone')
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
                
                Section::make('Datos de Pago')
                    ->schema([
                        Repeater::make('installments')
                            ->columns([
                                'sm' => 1,
                                'md' => 3,
                            ])
                            ->minItems(1)
                            ->maxItems(function (Get $get): int {
                                $membership = Membership::find($get('membership_id'));
                                return $membership->max_installments ?? 1;
                            })
                            ->relationship('payments')
                            ->label(fn (Get $get): string => self::updatePaymentStatus($get))
                            ->schema([
                                TextInput::make('amount')
                                    ->prefix('Bs.')
                                    ->live()
                                    ->required(),
                                Select::make('method')
                                    ->options([
                                        'cash' => 'Efectivo',
                                        'card' => 'Tarjeta',
                                    ])
                                    ->required(),
                                DateTimePicker::make('created_at')
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
                TextColumn::make('clients.name')
                    ->searchable(['name', 'card_id']),
                TextColumn::make('membership.name'),
                TextColumn::make('end_date')
                    ->dateTime('d-m-Y'),
                TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'expires_soon' => 'warning',
                    'expires_today' => 'danger',
                    'expired' => 'gray',
                })
            ])
            ->filters([
                SelectFilter::make('membership.name')
                    ->relationship('membership', 'name')
                    ->label('Membresia'),
            ])
            ->recordActions([
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

                ViewAction::make(),
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
                Section::make('Datos de la Suscripción')
                    ->headerActions([
                        Action::make('edit')
                            ->label('Editar')
                            ->url(fn (Subscription $record): string => SubscriptionResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        TextEntry::make('clients.name'),
                        TextEntry::make('membership.name'),
                        TextEntry::make('total_paid')
                            ->label('Total Pagado')
                            ->prefix('Bs. ')
                            ->color(fn (string $state, $record): string => $state >= $record->price ? 'success' : 'warning')
                            ->hint(fn (string $state, $record): string => $state >= $record->price ? 'Pago Completo' : 'Pago Pendiente'),
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
                        TextEntry::make('price'),
                        TextEntry::make('created_by'),
                        TextEntry::make('updated_by'),
                    ])
                    ->columns(3),

                Section::make('Pagos')
                    ->headerActions([
                        Action::make('addPayment')
                            ->label('Agregar Pago')
                            ->schema([
                                TextInput::make('amount')
                                    ->prefix('Bs.')
                                    ->required(),
                                Select::make('method')
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
                        RepeatableEntry::make('payments')
                            ->label('')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('amount'),
                                TextEntry::make('method'),
                                TextEntry::make('created_at')
                                    ->dateTime('d-m-Y H:i'),
                                TextEntry::make('created_by'),
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
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'edit' => EditSubscription::route('/{record}/edit'),
            'view' => ViewSubscription::route('/{record}'),
        ];
    }

    public static function updatePaymentStatus($get)
    {
        $membership = Membership::find($get('membership_id'));
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
