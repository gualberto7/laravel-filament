<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToGym;

class Client extends Model
{
    use BelongsToGym, HasFactory, HasUuids;

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }

    public function latestSubscription()
    {
        return $this->belongsToMany(Subscription::class)->orderByDesc('end_date');
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function getActiveSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->with('membership')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderByDesc('end_date')
            ->first();
    }

    public function checkInsCountForSubscription(Subscription $subscription): int
    {
        return $this->checkIns()
            ->where('subscription_id', $subscription->id)
            ->count();
    }

    public function addCheckIn($locker_number = null): CheckIn
    {
        $subscription = $this->getActiveSubscription();

        if (! $subscription) {
            throw new \RuntimeException('El cliente no tiene una suscripción activa.');
        }

        $membership = $subscription->membership;

        if ($membership->max_checkins !== null) {
            $used = $this->checkInsCountForSubscription($subscription);
            if ($used >= $membership->max_checkins) {
                throw new \RuntimeException(
                    "El cliente ha agotado sus {$membership->max_checkins} ingresos disponibles para este período."
                );
            }
        }

        return CheckIn::create([
            'client_id' => $this->id,
            'gym_id' => auth()->user()->getCurrentGymId(),
            'subscription_id' => $subscription->id,
            'locker_number' => $locker_number,
            'created_by' => auth()->user()->name,
            'updated_by' => auth()->user()->name,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('end_date', '>=', now());
        });
    }

    public function scopeInactive($query)
    {
        return $query->whereDoesntHave('subscriptions', function ($q) {
            $q->where('end_date', '>=', now());
        });
    }

    public function scopeNew($query)
    {
        return $query->where('created_at', '<=', now())->where('created_at', '>=', now()->subDays(30));
    }
}
