<?php

namespace App\Filament\Resources\MembershipResource\Pages;

use App\Filament\Resources\MembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembership extends EditRecord
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
