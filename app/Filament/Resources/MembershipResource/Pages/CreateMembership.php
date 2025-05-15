<?php

namespace App\Filament\Resources\MembershipResource\Pages;

use App\Filament\Resources\MembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['gym_id'] = auth()->user()->gym->id;
        return $data;
    }
}
