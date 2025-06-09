<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToGym
{
    protected static function booted()
    {
        static::addGlobalScope('gym', function (Builder $builder) {
            $builder->where('gym_id', auth()->user()->ownedGym->id);
        });
    }
} 