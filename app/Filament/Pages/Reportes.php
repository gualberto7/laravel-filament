<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClientsPerMonthChart;
use App\Filament\Widgets\SubscriptionsByMembershipChart;
use Filament\Pages\Page;

class Reportes extends Page
{
    protected static ?string $navigationLabel = 'Reportes';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.reportes';

    protected function getHeaderWidgets(): array
    {
        return [
            ClientsPerMonthChart::class,
            SubscriptionsByMembershipChart::class,
        ];
    }
}
