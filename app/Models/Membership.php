<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;
use App\Models\Traits\BelongsToGym;

class Membership extends Model
{
    use HasFactory, HasUuids, BelongsToGym;

    protected $casts = [
        'active' => 'boolean',
        'is_promo' => 'boolean',
        'promo_start_date' => 'date',
        'promo_end_date' => 'date',
    ];

    public function gym()
    {
        return $this->belongsTo(Gym::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($q) {
                // Para membresías normales
                $q->where('is_promo', false)
                  ->where('active', true);
            })->orWhere(function ($q) {
                // Para promociones
                $q->where('is_promo', true)
                  ->where('active', true)
                  ->where('promo_start_date', '<=', now())
                  ->whereDate('promo_end_date', '>=', now()->toDateString());
            });
        });
    }

    public function scopeInactive($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($q) {
                // Para membresías normales
                $q->where('is_promo', false)
                  ->where('active', false);
            })->orWhere(function ($q) {
                // Para promociones
                $q->where('is_promo', true)
                  ->where(function ($q) {
                      $q->where('active', false)
                        ->orWhere('promo_start_date', '>', now())
                        ->orWhereDate('promo_end_date', '<', now()->toDateString());
                  });
            });
        });
    }

    public function getIsActiveAttribute()
    {
        if (!$this->active) {
            return false;
        }

        if (!$this->is_promo) {
            return true;
        }

        $today = now()->toDateString();
        return $today >= $this->promo_start_date->format('Y-m-d') && 
               $today <= $this->promo_end_date->format('Y-m-d');
    }

    public static function getActivePromosQuery($query)
    {
        return $query->where(function ($query) {
            $query->where('is_promo', false)
                  ->orWhere(function ($query) {
                      $query->where('is_promo', true)
                            ->whereDate('promo_end_date', '>=', now()->toDateString());
                  });
        });
    }
}
