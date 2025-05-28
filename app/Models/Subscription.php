<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToGym;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory, HasUuids, BelongsToGym;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = ['status', 'total_paid'];

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
        $now = Carbon::now()->startOfDay();
        $endDate = $this->end_date->startOfDay();
        $daysDiff = (int) $now->diffInDays($endDate, false);
        
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

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }
}
