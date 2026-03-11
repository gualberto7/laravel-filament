<?php

namespace App\Models;

use Database\Factories\CheckInFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToGym;
use Carbon\Carbon;

class CheckIn extends Model
{
    /** @use HasFactory<CheckInFactory> */
    use BelongsToGym, HasFactory, HasUuids;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
