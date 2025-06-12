<?php

namespace App\Filament\Traits;

use Filament\Tables\Table;

trait HasPagination
{
    protected const DEFAULT_PAGINATION_PAGE_OPTION = 15;
    protected const PAGINATION_OPTIONS = [10, 15, 25, 50, 100, 'all'];

    protected static function applyPagination(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(self::DEFAULT_PAGINATION_PAGE_OPTION)
            ->paginated(self::PAGINATION_OPTIONS);
    }
} 