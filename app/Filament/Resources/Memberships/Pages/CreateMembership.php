<?php

namespace App\Filament\Resources\Memberships\Pages;

use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;

    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->extraAttributes(['data-test' => 'create-membership-button']);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['gym_id'] = auth()->user()->getCurrentGymId();
        $data['created_by'] = auth()->user()->name;
        $data['updated_by'] = auth()->user()->name;
        // Remove has_max_checkins from data
        unset($data['has_max_checkins']);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Membresía creado')
            ->body('La membresía fue creado correctamente');
    }
}
