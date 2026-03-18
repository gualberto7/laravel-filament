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
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Livewire\Account\Settings as AccountSettingsLivewire;
use App\Livewire\Gym\Settings as SettingsLivewire;

class Settings extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Gimnasio';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $title = 'Configuración';

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
                Section::make('Gimnasio')
                    ->description('Configuración del gimnasio')
                    ->aside()
                    ->schema([
                        Livewire::make(SettingsLivewire::class, [
                            'currentGym' => $this->currentGym,
                        ]),
                    ]),
                Section::make('Cuenta')
                    ->description('Configuración de tu cuenta')
                    ->aside()
                    ->schema([
                        Livewire::make(AccountSettingsLivewire::class),
                    ]),
            ]);
    }
}
