<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['status'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function getStatusAttribute()
    {
        $daysDiff = $this->end_date->diffInDays(now());

        if ($daysDiff < 0) {
            return 'expired';
        }
        if ($daysDiff === 0) {
            return 'expires_today';
        }
        if ($daysDiff <= 3) {
            return 'expires_soon';
        }
        return 'active';
    }
}
