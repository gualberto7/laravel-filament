<?php

namespace App\Filament\Resources\Subscriptions;

use Carbon\Carbon;
use App\Enums\PaymentMethod;
use App\Models\Client;
use App\Models\Membership;
use App\Models\Subscription;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Traits\HasPagination;
use Illuminate\Validation\Rule;
use App\Filament\Resources\Subscriptions\Pages\EditSubscription;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscription;
use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\CreateSubscription;

class SubscriptionResource extends Resource
{
    use HasPagination;

    protected static ?string $model = Subscription::class;

    protected static bool $shouldRegisterNavigation = false;

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
                            ->readOnly(),
                    ])
                    ->columnSpanFull(),

                Section::make('Datos del Cliente')
                    ->schema([
                        Select::make('clients')
                            ->relationship('clients', 'name')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('card_id')
                                    ->label('Nro. de carnet')
                                    ->required()
                                    ->rules([
                                        Rule::unique('clients', 'card_id')
                                            ->where('gym_id', auth()->user()->getCurrentGymId()),
                                    ])
                                    ->validationMessages([
                                        'unique' => 'Ya existe un cliente con este número de carnet en este gimnasio.',
                                    ]),
                                TextInput::make('phone')
                                    ->label('Celular')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): string {
                                $client = Client::create($data + [
                                    'gym_id' => auth()->user()->getCurrentGymId(),
                                ]);

                                return $client->id;
                            })
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => $record->name.' - '.$record->card_id)
                            ->searchable(['name', 'card_id'])
                            ->multiple()
                            ->required()
                            ->disabledOn(['edit'])
                            ->minItems(1),
                    ])
                    ->columnSpanFull(),

                Section::make('Datos de Pago')
                    ->schema([
                        Repeater::make('installments')
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
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
                                    ->options(PaymentMethod::class)
                                    ->required(),
                            ])
                            ->cloneable()
                            ->addActionLabel('Agregar Pago'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->groups([
                'membership.name',
            ])
            ->columns([
                TextColumn::make('clients.name')
                    ->searchable(['name', 'card_id']),
                TextColumn::make('membership.name'),
                TextColumn::make('end_date')
                    ->dateTime('d-m-Y'),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('membership.name')
                    ->relationship('membership', 'name')
                    ->label('Membresia'),
                SelectFilter::make('client')
                    ->relationship('clients', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->preload(),
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
                        TextEntry::make('clients.name')
                            ->label('Cliente'),
                        TextEntry::make('membership.name')
                            ->label('Membresía'),
                        TextEntry::make('total_paid')
                            ->label('Total Pagado')
                            ->prefix('Bs. ')
                            ->color(fn (string $state, $record): string => $state >= $record->price ? 'success' : 'warning')
                            ->hint(fn (string $state, $record): string => $state >= $record->price ? 'Pago Completo' : 'Pago Pendiente'),
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge(),
                        TextEntry::make('start_date')
                            ->label('Fecha inicio')
                            ->dateTime('d-m-Y'),
                        TextEntry::make('end_date')
                            ->label('Fecha fin')
                            ->dateTime('d-m-Y'),
                        TextEntry::make('price')
                            ->label('Precio'),
                        TextEntry::make('created_by')
                            ->label('Registrado por'),
                        TextEntry::make('updated_by')
                            ->label('Actualizado por'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Pagos')
                    ->headerActions([
                        Action::make('addPayment')
                            ->label('Agregar Pago')
                            ->disabled(fn (Subscription $record): bool => $record->total_paid >= $record->price)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('monto')
                                    ->prefix('Bs.')
                                    ->required(),
                                Select::make('method')
                                    ->label('Metodo de pago')
                                    ->options(PaymentMethod::class)
                                    ->required(),
                            ])
                            ->action(function (array $data, Subscription $record, $livewire) {
                                $record->payments()->create($data);
                                Notification::make()
                                    ->title('Pago agregado correctamente')
                                    ->success()
                                    ->send();
                                $livewire->dispatch('payment-created');
                            })
                            ->modalWidth('md'),
                    ])
                    ->schema([
                        RepeatableEntry::make('payments')
                            ->label('Pagos de la suscripción')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('amount')
                                    ->label('Monto'),
                                TextEntry::make('method')
                                    ->label('Método de pago')
                                    ->formatStateUsing(fn (string $state) => PaymentMethod::from($state)->getLabel()),
                                TextEntry::make('created_at')
                                    ->label('Fecha y hora')
                                    ->dateTime('d-m-Y H:i'),
                                TextEntry::make('created_by')
                                    ->label('Registrado por'),
                            ]),
                    ])
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
            return 'Pagado: '.$totalAmount.' de '.$membership->price;
        }

        return '';
    }
}
