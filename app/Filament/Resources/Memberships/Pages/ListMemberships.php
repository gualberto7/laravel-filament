<?php

namespace App\Filament\Resources\Memberships\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Memberships\MembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMemberships extends ListRecords
{
    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos'),
            'active' => Tab::make('Activos')
                ->modifyQueryUsing(fn (Builder $query) => $query->active()),
            'inactive' => Tab::make('Inactivos')
                ->modifyQueryUsing(fn (Builder $query) => $query->inactive()),
        ];
    }
}
