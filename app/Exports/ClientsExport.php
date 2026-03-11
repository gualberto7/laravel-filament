<?php

namespace App\Exports;

use App\Exports\Sheets\ClientsSheet;
use App\Exports\Sheets\SubscriptionsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientsExport implements WithMultipleSheets
{
    public function __construct(private readonly string $gymId) {}

    public function sheets(): array
    {
        return [
            new ClientsSheet($this->gymId),
            new SubscriptionsSheet($this->gymId),
        ];
    }
}
