<?php

namespace App\Filament\Resources\CheckIns;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CheckIns\Pages\ListCheckIns;
use App\Filament\Resources\CheckIns\Pages\CreateCheckIn;
use App\Filament\Resources\CheckIns\Pages\EditCheckIn;
use App\Filament\Resources\CheckInResource\Pages;
use App\Filament\Resources\CheckInResource\RelationManagers;
use App\Models\CheckIn;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Carbon\Carbon;
use App\Filament\Traits\HasPagination;

class CheckInResource extends Resource
{
    use HasPagination;

    protected static ?string $model = CheckIn::class;

    protected static ?string $navigationLabel = 'Check-ins';
    protected static ?int $navigationSort = 2;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-circle';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::applyPagination($table)
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->orderByDesc('created_at');
            })
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->dateTime('D d - H:i'),
                TextColumn::make('locker_number')
                    ->label('Caja')
                    ->searchable(),
                TextColumn::make('created_by')
                    ->label('Creado por'),
            ])
            ->filters([
                Filter::make('created_at')
                ->schema([
                    DatePicker::make('created_from')
                        ->label('Desde')
                        ->default(now()),
                    DatePicker::make('created_until')
                        ->label('Hasta')
                        ->default(now()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCheckIns::route('/'),
            'create' => CreateCheckIn::route('/create'),
            'edit' => EditCheckIn::route('/{record}/edit'),
        ];
    }
}
