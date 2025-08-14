<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Models\Subscription;

use Filament\Forms;
use Livewire\Component;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\SubscriptionResource;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Notifications\Notification;

class Search extends Component implements HasForms, HasInfolists
{
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->subscription)
            ->schema([
                Infolists\Components\Section::make($this->client->name)
                    ->description('CI: ' . $this->client->card_id)
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('show')
                            ->label('Ver Detalle')
                            ->url(fn (Subscription $record): string => SubscriptionResource::getUrl('view', ['record' => $record])),
                    ])
                    ->footerActions([
                        Infolists\Components\Actions\Action::make('checkIn')
                            ->disabled(fn (): bool => $this->subscription?->status === 'expired')
                            ->action(function () {
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
                            }),
                        Infolists\Components\Actions\Action::make('createSubscription')
                            ->label('Nueva Suscripción')
                            ->visible(fn (): bool => $this->subscription?->status === 'expired')
                            ->url(fn (): string => SubscriptionResource::getUrl('create', ['client_id' => $this->client->id]))
                    ])
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'expired' => 'danger',
                                default => 'warning',
                            }),
                        Infolists\Components\TextEntry::make('membership.name'),
                        Infolists\Components\TextEntry::make('start_date')
                            ->dateTime('d-m-Y'),
                        Infolists\Components\TextEntry::make('end_date')
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
