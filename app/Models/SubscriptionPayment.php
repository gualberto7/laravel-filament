<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionPayment extends Model
{
    use HasFactory, HasUuids;

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
