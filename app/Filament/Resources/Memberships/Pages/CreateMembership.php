<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['gym_id'] = auth()->user()->getCurrentGymId();
        $data['created_by'] = auth()->user()->name;
        $data['updated_by'] = auth()->user()->name;
        // Remove has_max_checkins from data
        unset($data['has_max_checkins']);
        return $data;
    }
}
