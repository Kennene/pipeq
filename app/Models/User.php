<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function hasTickets(): bool
    {
        return $this->tickets()->exists();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role): bool
    {
        // if user has the Administrator role, they have all roles
        if ($this->roles()->where('name', 'Administrator')->exists()) {
            return true;
        }

        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Returns the model Role with biggest id
     * 
     * todo: this is shitty system actually, needs overhaul
     * Our system is designed with ability of user to have multiple roles.
     * However, since they are cascade, only one is the most important
     * @depracated
     */
    // public function biggestRole(): Role
    // {
    //     return $this->roles()->orderBy('id', 'desc')->first();
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
}
