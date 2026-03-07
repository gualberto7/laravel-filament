<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionPayment extends Model
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::creating(function (SubscriptionPayment $payment) {
            $name = auth()->user()?->name ?? 'system';
            $payment->created_by = $name;
            $payment->updated_by = $name;
        });
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
