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
}
