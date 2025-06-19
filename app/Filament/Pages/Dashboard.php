<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CheckinsState;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;
use App\Models\Client;

class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    public $currentGym;

    public function mount()
    {
        $this->currentGym = auth()->user()->current_gym;
    }

    protected function getHeaderWidgets(): array
     {
         return [
            StatsOverview::class,
         ];
     }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->options(Client::all()->pluck('name', 'id'))
                    ->searchable()
            ]);
    }
}
