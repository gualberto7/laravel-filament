<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\CheckIn;
use App\Models\Client;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Actividad', CheckIn::today()->count())
                ->description('Ingresos al Gimnasio hoy')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make('Clientes activos', Client::active()->count())
                ->description('Clientes con suscripción activa')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Nuevos clientes', Client::new()->count())
                ->description('Nuevos clientes este mes')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('primary'),
        ];
    }
}
