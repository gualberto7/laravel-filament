<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gym()
    {
        return $this->hasOne(Gym::class);
    }

    public function gyms(): BelongsToMany
    {
        return $this->belongsToMany(Gym::class)
            ->withPivot(['role', 'permissions', 'is_active'])
            ->withTimestamps();
    }

    public function ownedGyms(): HasMany
    {
        return $this->hasMany(Gym::class, 'user_id');
    }

    public function isGymOwner(Gym $gym): bool
    {
        return $this->id === $gym->user_id;
    }

    public function isGymStaff(Gym $gym): bool
    {
        return $this->gyms()
            ->where('gym_id', $gym->id)
            ->wherePivot('is_active', true)
            ->exists();
    }

    public function getGymRole(Gym $gym): ?string
    {
        return $this->gyms()
            ->where('gym_id', $gym->id)
            ->wherePivot('is_active', true)
            ->value('role');
    }
}
