<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\CheckIns\CheckInResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Traits\HasPagination;
use App\Models\CheckIn;
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

class ClientCheckIns extends Page implements HasTable
{
    use HasPagination;
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.client-check-ins';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return CheckInResource::getPluralModelLabel().' de '.$this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        return [
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
                CheckIn::query()
                    ->where('client_id', $this->getRecord()->id)
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                TextColumn::make('updated_at')
                    ->label('Hora')
                    ->dateTime('H:i'),
                TextColumn::make('locker_number')
                    ->label('Casillero')
                    ->default('—'),
                TextColumn::make('created_by')
                    ->label('Registrado por'),
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
            ]);
    }
}
