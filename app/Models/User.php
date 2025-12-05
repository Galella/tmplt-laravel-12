<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Office;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'office_id',
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

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->count() === count($roleNames);
    }

    /**
     * Get the offices for the user.
     */
    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'office_user')
                    ->withPivot(['position', 'assigned_date', 'is_primary', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Get the user's primary office.
     */
    public function primaryOffice()
    {
        return $this->offices()->wherePivot('is_primary', true)->first();
    }

    /**
     * Get the user's position in a specific office.
     */
    public function positionInOffice(Office $office)
    {
        $pivot = $this->offices()->where('office_id', $office->id)->first()?->pivot;
        return $pivot ? $pivot->position : null;
    }

    /**
     * Get the primary office for the user (direct relationship).
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
