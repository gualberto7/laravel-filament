<?php

namespace App\Exports\Sheets;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClientsSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private readonly string $gymId) {}

    public function title(): string
    {
        return 'Clientes';
    }

    public function query()
    {
        return Client::query()->where('gym_id', $this->gymId)->orderBy('name');
    }

    public function headings(): array
    {
        return ['Nombre', 'Nro. Carnet', 'Celular', 'Email', 'Fecha de Registro'];
    }

    public function map($client): array
    {
        return [
            $client->name,
            $client->card_id,
            $client->phone,
            $client->email,
            $client->created_at->format('Y-m-d'),
        ];
    }
}
