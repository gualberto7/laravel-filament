<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Gym extends Model
{
    use HasFactory, HasUuids;
    
    public function user()
    {
        return $this->belongsTo(User::class);
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
