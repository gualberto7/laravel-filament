<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClientsPerMonthChart;
use App\Filament\Widgets\SubscriptionsByMembershipChart;
use Filament\Pages\Page;

class SubscriptionsReport extends Page
{
    protected static ?string $navigationLabel = 'Subscripciones';

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Reportes de Subscripciones';

    protected string $view = 'filament.pages.subscriptions-report';

    protected function getHeaderWidgets(): array
    {
        return [
            ClientsPerMonthChart::class,
            SubscriptionsByMembershipChart::class,
        ];
    }
}
