<?php

namespace App\Filament\SuperAdmin\Resources\GymResource\Pages;

use App\Filament\SuperAdmin\Resources\GymResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGym extends EditRecord
{
    protected static string $resource = GymResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
