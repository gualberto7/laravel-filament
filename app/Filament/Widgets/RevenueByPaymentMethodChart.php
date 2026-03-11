<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionPayment;
use Filament\Widgets\ChartWidget;

class RevenueByPaymentMethodChart extends ChartWidget
{
    protected ?string $heading = 'Ingresos por método de pago';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $gymId = auth()->user()->getCurrentGymId();

        $results = SubscriptionPayment::whereHas(
            'subscription',
            fn ($q) => $q->where('gym_id', $gymId)
        )
            ->where('status', 'paid')
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $labels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'qr' => 'QR',
            'bank_transfer' => 'Transferencia bancaria',
            'cheque' => 'Cheque',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $results->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
                ],
            ],
            'labels' => $results->pluck('method')->map(fn ($m) => $labels[$m] ?? $m)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
