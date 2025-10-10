<?php

namespace App\Filament\Resources\Memberships\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembership extends EditRecord
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->user()->name;
        // Remove has_max_checkins from data
        unset($data['has_max_checkins']);
        return $data;
    }
}
