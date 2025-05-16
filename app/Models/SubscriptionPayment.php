<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
