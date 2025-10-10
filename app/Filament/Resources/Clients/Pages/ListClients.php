<?php

namespace App\Filament\Resources\Clients\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

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
