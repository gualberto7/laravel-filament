<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionPayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenuePerMonthChart extends ChartWidget
{
    protected ?string $heading = 'Ingresos por mes';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $gymId = auth()->user()->getCurrentGymId();

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $data = $months->map(fn (Carbon $month) => SubscriptionPayment::whereHas(
            'subscription',
            fn ($q) => $q->where('gym_id', $gymId)
        )
            ->where('status', 'paid')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->sum('amount')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $data->values()->toArray(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $months->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
