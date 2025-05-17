<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    /** @use HasFactory<\Database\Factories\CheckInFactory> */
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }
}
