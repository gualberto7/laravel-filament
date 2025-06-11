<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\HasPreferences;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles, HasPreferences;

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
        return $this->belongsTo(Gym::class);
    }

    public function ownedGym() : HasOne
    {
        return $this->hasOne(Gym::class);
    }

    public function setCurrentGym()
    {
        $gym = $this->ownedGym;

        if (!$gym) {
            $gym = $this->gym;
        }

        if (!$gym) {
            throw new \Exception('User does not have an owned gym or gym');
        }

        $this->setPreference('current_gym', $gym->id);

        return $gym->id;
    }

    public function getCurrentGymId()
    {
        $gymId = $this->getPreference('current_gym');

        if (!$gymId) {
            $this->setCurrentGym();
            return $this->getPreference('current_gym');
        }

        return $gymId;
    }
}
