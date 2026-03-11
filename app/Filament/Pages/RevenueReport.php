<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RevenueByPaymentMethodChart;
use App\Filament\Widgets\RevenuePerMonthChart;
use Filament\Pages\Page;

class RevenueReport extends Page
{
    protected static ?string $navigationLabel = 'Ingresos';

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Reportes de Ingresos';

    protected string $view = 'filament.pages.revenue-report';

    protected function getHeaderWidgets(): array
    {
        return [
            RevenuePerMonthChart::class,
            RevenueByPaymentMethodChart::class,
        ];
    }
}
