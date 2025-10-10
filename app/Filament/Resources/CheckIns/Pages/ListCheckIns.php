<?php

namespace App\Filament\Resources\CheckIns\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CheckIns\CheckInResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCheckIns extends ListRecords
{
    protected static string $resource = CheckInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
