<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Filament\Traits\HasPagination;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientSubscriptions extends Page implements HasTable
{
    use HasPagination;
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.client-subscriptions';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return 'Suscripciones de '.$this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Nueva Suscripción')
                ->url(SubscriptionResource::getUrl('create', ['client_id' => $this->getRecord()->id])),
            Action::make('back')
                ->label('Volver al cliente')
                ->url(ClientResource::getUrl('view', ['record' => $this->getRecord()]))
                ->color('gray'),
        ];
    }

    public function table(Table $table): Table
    {
        return self::applyPagination($table)
            ->query(
                Subscription::query()
                    ->whereHas('clients', fn (Builder $query) => $query->where('clients.id', $this->getRecord()->id))
                    ->with('membership')
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('membership.name')
                    ->label('Membresía'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('start_date')
                    ->label('Inicio')
                    ->dateTime('d-m-Y'),
                TextColumn::make('end_date')
                    ->label('Vencimiento')
                    ->dateTime('d-m-Y'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->prefix('Bs. '),
            ])
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Desde'),
                        DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordUrl(fn (Subscription $record): string => SubscriptionResource::getUrl('view', ['record' => $record]));
    }
}
