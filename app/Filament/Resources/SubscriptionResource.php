<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Carbon\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('membership_id')
                    ->relationship('membership', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $membership = \App\Models\Membership::find($state);
                            if ($membership) {
                                $set('end_date', now()->addDays($membership->duration)->format('Y-m-d'));
                                $set('price', $membership->price);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->readOnly(),
                Forms\Components\DatePicker::make('start_date')
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        if ($state) {
                            $membership = \App\Models\Membership::find($get('membership_id'));
                            if ($membership) {
                                $set('end_date', Carbon::parse($state)->addDays($membership->duration)->format('Y-m-d'));
                            }
                        }
                    })
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->readOnly()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
