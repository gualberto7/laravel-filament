<?php

namespace App\Models\Traits;

use App\Models\Preference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasPreferences
{
    public function preferences(): MorphMany
    {
        return $this->morphMany(Preference::class, 'preferable');
    }

    public function getPreference(string $key)
    {
        $cacheKey = "preferable.{$this->id}.{$key}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($key) {
            $preference = $this->preferences()->where('key', $key)->first();
            return $preference ? $preference->value : null;
        });
    }

    public function setPreference(string $key, $value)
    {
        $cacheKey = "preferable.{$this->id}.{$key}";

        $this->preferences()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget($cacheKey);
    }

    public function getPreferenceValue(string $key)
    {
        return $this->getPreference($key);
    }
}