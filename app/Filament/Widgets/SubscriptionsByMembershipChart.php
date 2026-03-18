<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class SubscriptionsByMembershipChart extends ChartWidget
{
    protected ?string $heading = 'Suscripciones por membresía';

    protected ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $gymId = auth()->user()->getCurrentGymId();

        $results = Subscription::withoutGlobalScopes()
            ->join('memberships', 'subscriptions.membership_id', '=', 'memberships.id')
            ->selectRaw('memberships.name, COUNT(*) as total')
            ->where('subscriptions.gym_id', $gymId)
            ->groupBy('memberships.id', 'memberships.name')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Suscripciones',
                    'data' => $results->pluck('total')->toArray(),
                ],
            ],
            'labels' => $results->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
