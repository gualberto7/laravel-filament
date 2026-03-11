<?php

namespace App\Exports\Sheets;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubscriptionsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private readonly string $gymId) {}

    public function title(): string
    {
        return 'Suscripciones';
    }

    public function collection(): Collection
    {
        $activeStatuses = ['active', 'expires_soon', 'expires_today'];

        $clients = Client::query()
            ->where('gym_id', $this->gymId)
            ->with(['subscriptions' => fn ($q) => $q->with('membership')->orderByDesc('end_date')])
            ->orderBy('name')
            ->get();

        return $clients
            ->map(function (Client $client) use ($activeStatuses) {
                $subscription = $client->subscriptions
                    ->first(fn ($s) => in_array($s->status, $activeStatuses))
                    ?? $client->subscriptions->first();

                if (! $subscription) {
                    return null;
                }

                return ['client' => $client, 'subscription' => $subscription];
            })
            ->filter()
            ->values();
    }

    public function headings(): array
    {
        return ['Cliente', 'Membresía', 'Fecha Inicio', 'Fecha Fin', 'Estado', 'Precio'];
    }

    public function map($row): array
    {
        $client = $row['client'];
        $subscription = $row['subscription'];

        return [
            $client->name,
            $subscription->membership->name ?? '-',
            $subscription->start_date->format('Y-m-d'),
            $subscription->end_date->format('Y-m-d'),
            $subscription->status,
            $subscription->membership->price ?? 0,
        ];
    }
}
