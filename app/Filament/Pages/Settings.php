<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use App\Models\Gym;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use App\Livewire\Gym\Settings as SettingsLivewire;

class Settings extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    public $currentGymId;
    public $currentGym;

    public function mount()
    {
        $this->currentGymId = Filament::auth()->user()->getCurrentGymId();
        $this->currentGym = Gym::find($this->currentGymId);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->currentGym)
            ->schema([
                Infolists\Components\Section::make('Gym Settings')
                    ->description('Configuración de la sucursal')
                    ->aside()
                    ->schema([
                        Infolists\Components\Livewire::make(SettingsLivewire::class, [
                            'currentGym' => $this->currentGym,
                        ])
                    ])
            ]);
    }
}
