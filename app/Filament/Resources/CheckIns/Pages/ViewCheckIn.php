<?php

namespace App\Filament\Resources\CheckIns\Pages;

use App\Filament\Resources\CheckIns\CheckInResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCheckIn extends ViewRecord
{
    protected static string $resource = CheckInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
