<?php

namespace App\Livewire\Client;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use App\Models\Client;
use App\Models\Subscription;
use Filament\Forms\Components\TextInput;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Notifications\Notification;

class Search extends Component implements HasForms, HasActions, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    public $currentGym;
    public $search;
    public $data = [];
    public $client;
    public $subscription;

    public function mount($currentGym)
    {
        $this->currentGym = $currentGym;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Cliente')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $this->client = Client::find($state);
                            $this->subscription = $this->client->subscriptions()->latest()->first();
                        } else {
                            $this->client = null;
                            $this->subscription = null;
                        }
                    })
            ])
            ->statePath('data');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->subscription)
            ->components([
                Section::make($this->client?->name ?? 'Cliente')
                    ->description('CI: ' . ($this->client->card_id ?? ''))
                    ->headerActions([
                        Action::make('show')
                            ->label('Ver Detalle')
                            ->url(fn (Subscription $record): string => SubscriptionResource::getUrl('view', ['record' => $record])),
                    ])
                    ->footerActions([
                        Action::make('checkIn')
                            ->label('registro')
                            ->disabled(fn (): bool => $this->subscription?->status === 'expired')
                            ->action(function (array $data) {
                                $checkIn = $this->client->addCheckIn();
                                
                                if ($checkIn) {
                                    Notification::make()
                                        ->title('Check-in registrado correctamente')
                                        ->body('El check-in para ' . $this->client->name . ' ha sido registrado exitosamente.')
                                        ->success()
                                        ->send();
                                    
                                    // Reset form and clear data
                                    $this->form->fill(['client_id' => null]);
                                    $this->client = null;
                                    $this->subscription = null;
                                } else {
                                    Notification::make()
                                        ->title('Ocurrio un error')
                                        ->error()
                                        ->send();
                                }
                            })
                            ->modalWidth('md'),
                        Action::make('createSubscription')
                            ->label('Nueva Suscripción')
                            ->visible(fn (): bool => $this->subscription?->status === 'expired')
                            ->url(fn (): string => SubscriptionResource::getUrl('create', ['client_id' => $this->client->id]))
                    ])
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'expired' => 'danger',
                                default => 'warning',
                            }),
                        TextEntry::make('membership.name'),
                        TextEntry::make('start_date')
                            ->dateTime('d-m-Y'),
                        TextEntry::make('end_date')
                            ->dateTime('d-m-Y'),
                    ])
                    ->columns(2),
            ]);
    }

    public function render()
    {
        return view('livewire.client.search');
    }
}
