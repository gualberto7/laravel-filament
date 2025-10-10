<?php

namespace App\Filament\SuperAdmin\Resources\Gyms\Pages;

use Filament\Actions\CreateAction;
use App\Filament\SuperAdmin\Resources\Gyms\GymResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGyms extends ListRecords
{
    protected static string $resource = GymResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
