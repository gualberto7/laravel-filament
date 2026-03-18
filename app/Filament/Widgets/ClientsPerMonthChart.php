<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ClientsPerMonthChart extends ChartWidget
{
    protected ?string $heading = 'Suscripciones registradas por mes';

    protected ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $data = $months->map(fn (Carbon $month) => Subscription::whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count()
        );

        return [
            'datasets' => [
                [
                    'label' => 'Suscripciones',
                    'data' => $data->values()->toArray(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $months->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
