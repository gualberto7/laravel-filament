<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Livewire;
use Filament\Pages\Page;
use Filament\Facades\Filament;
use App\Models\Gym;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use App\Livewire\Gym\Settings as SettingsLivewire;

class Settings extends Page implements HasInfolists, HasForms
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Gimnasio';
    protected static string | \UnitEnum | null $navigationGroup = 'Configuración';

    protected string $view = 'filament.pages.settings';

    public $currentGymId;
    public $currentGym;

    public function mount()
    {
        $this->currentGymId = Filament::auth()->user()->getCurrentGymId();
        $this->currentGym = Gym::find($this->currentGymId);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->currentGym)
            ->components([
                Section::make('Gym Settings')
                    ->description('Configuración de la sucursal')
                    ->aside()
                    ->schema([
                        Livewire::make(SettingsLivewire::class, [
                            'currentGym' => $this->currentGym,
                        ])
                    ])
            ]);
    }
}
