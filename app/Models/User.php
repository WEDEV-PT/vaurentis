<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasEmailAuthentication
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithAppAuthentication, InteractsWithEmailAuthentication, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active',
        'two_factor_required',
        'two_factor_method',
        'timezone',
        'brand_logo_path',
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
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'two_factor_required' => 'boolean',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if (! $user->two_factor_required) {
                $user->two_factor_method = null;
                $user->has_email_authentication = false;
                $user->app_authentication_secret = null;

                return;
            }

            if ($user->two_factor_method === 'email') {
                $user->has_email_authentication = true;
                $user->app_authentication_secret = null;

                return;
            }

            $user->has_email_authentication = false;

            if ($user->isDirty('two_factor_method')) {
                $user->app_authentication_secret = null;
            }
        });
    }

    public function projectAccesses()
    {
        return $this->hasMany(ProjectAccess::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }
}
