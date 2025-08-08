<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToGym;

class Client extends Model
{
    use HasFactory, HasUuids, BelongsToGym;

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function addCheckIn($locker_number = null)
    {
        $checkIn = CheckIn::create([
            'client_id' => $this->id,
            'gym_id' => auth()->user()->getCurrentGymId(),
            'locker_number' => $locker_number,
            'created_by' => auth()->user()->name,
            'updated_by' => auth()->user()->name,
        ]);

        return $checkIn;
    }

    public function scopeActive($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('end_date', '>=', now());
        });
    }

    public function scopeInactive($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('end_date', '<', now());
        });
    }

    public function scopeNew($query)
    {
        return $query->where('created_at', '<=', now())->where('created_at', '>=', now()->subDays(30));
    }
}
