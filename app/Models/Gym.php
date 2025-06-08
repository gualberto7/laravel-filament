<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'permissions', 'is_active'])
            ->withTimestamps();
    }

    public function activeStaff(): BelongsToMany
    {
        return $this->staff()->wherePivot('is_active', true);
    }

    public function admins(): BelongsToMany
    {
        return $this->staff()->wherePivot('role', 'admin');
    }

    public function trainers(): BelongsToMany
    {
        return $this->staff()->wherePivot('role', 'trainer');
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
