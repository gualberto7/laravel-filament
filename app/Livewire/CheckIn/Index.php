<?php

namespace App\Livewire\CheckIn;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Models\CheckIn;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;

class Index extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithForms;

    public $record;

    public function mount(Model $record)
    {
        $this->record = $record;
    }

    public function table(Table $table)
    {
        return $table
            ->query(CheckIn::query()->where('client_id', $this->record->id)->latest()->limit(5))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d-m-Y'),
                TextColumn::make('updated_at')
                    ->label('Hora')
                    ->dateTime('H:i'),
                TextColumn::make('created_by')
            ])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.check-in.index');
    }
}
