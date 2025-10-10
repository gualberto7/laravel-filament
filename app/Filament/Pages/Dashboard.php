<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CheckinsState;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Dashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.dashboard';

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
}
