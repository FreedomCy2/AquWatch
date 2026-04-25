<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'plan_tier',
        'plan_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_changed_at' => 'datetime',
        ];
    }

    public function profile()
    
    {
    return $this->hasOne(\App\Models\Profile::class);
    }

    public function sensors(): BelongsToMany
    {
        return $this->belongsToMany(Sensor::class, 'sensor_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'sent_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }

    public function initials(): string
    {
        return (string) collect(explode(' ', (string) $this->name))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }

    public function isPro(): bool
    {
        return strtolower((string) $this->plan_tier) === 'pro';
    }

    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }
}