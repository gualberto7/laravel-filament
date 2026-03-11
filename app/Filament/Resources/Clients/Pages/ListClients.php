<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Exports\ClientsExport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = 'Clientes';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $gymId = auth()->user()->getCurrentGymId();

                    return Excel::download(
                        new ClientsExport($gymId),
                        'clientes-'.now()->format('Y-m-d').'.xlsx'
                    );
                })
                ->visible(auth()->user()->hasRole('owner')),
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
