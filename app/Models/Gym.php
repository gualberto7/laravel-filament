<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gym extends Model
{
    use HasFactory;
    
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
