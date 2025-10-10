<?php

namespace App\Filament\SuperAdmin\Resources\Gyms\Pages;

use App\Filament\SuperAdmin\Resources\Gyms\GymResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGym extends CreateRecord
{
    protected static string $resource = GymResource::class;
}
