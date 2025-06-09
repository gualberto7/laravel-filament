<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gym extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'name',
        'address',
        'phone',
        'user_id', // owner
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function activeStaff(): BelongsToMany
    {
        return $this->staff()->where('is_active', true);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
}
