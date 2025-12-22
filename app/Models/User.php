<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
    ];

    protected $with = ['role'];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

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
            'last_login_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_string($role)) {
            return $this->role->name === $role;
        }

        return $this->role->id === $role->id;
    }

    public function hasPermission($permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('name', $permission)->exists();
    }

    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrFail();
        }

        $this->role()->associate($role);
        $this->save();
    }

    // removeRole is not really applicable for 1-to-1 (just assign a different one or null), but for interface compatibility:
    public function removeRole($role): void
    {
        $this->role()->dissociate();
        $this->save();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get the reports for the user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // Assign default 'user' role to new users
            $defaultRole = Role::where('name', 'user')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }

            // Create default preferences
            $user->preference()->create();
        });
    }
}
