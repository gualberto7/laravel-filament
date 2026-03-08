<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\CheckIn;
use App\Models\Client;
use App\Models\Subscription;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Actividad', CheckIn::today()->count())
                ->description('Ingresos al Gimnasio hoy')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Clientes activos', Client::active()->count())
                ->description('Clientes con suscripción activa')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Suscripciones este mes', Subscription::thisMonth()->count())
                ->description('Nuevas suscripciones en '.now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('success'),
        ];
    }
}
