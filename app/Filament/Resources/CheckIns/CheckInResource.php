<?php

namespace App\Filament\Resources\CheckIns;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\CheckIns\Pages\ListCheckIns;
use App\Filament\Resources\CheckIns\Pages\ViewCheckIn;
use App\Models\CheckIn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasPagination;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CheckInResource extends Resource
{
    use HasPagination;

    protected static ?string $model = CheckIn::class;

    protected static ?string $modelLabel = 'Check-in';

    protected static ?string $pluralModelLabel = 'Check-ins';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

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
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->dateTime('D d - H:i'),
                TextColumn::make('locker_number')
                    ->label('Casillero')
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
                    }),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalle de Check-In')
                    ->description('Detalle del check in del cliente')
                    ->schema([
                        TextEntry::make('client.name')
                            ->label('Nombre'),
                        TextEntry::make('created_at')
                            ->label('Fecha y Hora')
                            ->dateTime('d-m-Y H:i'),
                        TextEntry::make('locker_number')
                            ->label('Nro. de Casillero'),
                        TextEntry::make('subscription.membership.name')
                            ->label('Membresia'),
                        TextEntry::make('created_by')
                            ->label('Registrado por'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
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
            'view' => ViewCheckIn::route('/{record}'),
        ];
    }
}
