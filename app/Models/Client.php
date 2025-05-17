<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

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

    public function addCheckIn($gym_id, $locker_number = null)
    {
        $checkIn = CheckIn::create([
            'client_id' => $this->id,
            'gym_id' => $gym_id,
            'locker_number' => $locker_number,
            'created_by' => auth()->user()->name,
            'updated_by' => auth()->user()->name,
        ]);
    }
}
