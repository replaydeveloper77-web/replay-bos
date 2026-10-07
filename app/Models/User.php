<?php

namespace App\Models;

// Illuminate\Foundation\Auth\User as Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
        'customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * The Reseller customer record this login represents — only set for role "reseller"
     * accounts, scopes everything they can see in the Reseller Portal.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The HR master-data record for this login, if one exists — not every account
     * has one (e.g. accounts that aren't real staff), and not every Employee has
     * a login. Used to resolve who's checking in/out for Absensi.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Check whether this user holds the given permission slug through any of their roles.
     */
    public function hasPermission(string $slug): bool
    {
        return $this->roles->loadMissing('permissions')
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->contains($slug);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->pluck('slug')->contains($slug);
    }

    public function hasTwoFactorEnabled(): bool
    {
        if (! config('auth.two_factor_enabled', true)) {
            return false;
        }

        return $this->two_factor_confirmed_at !== null;
    }

    /**
     * Roles sensitive enough that 2FA isn't optional — enforced via EnsureTwoFactorIsEnabled middleware.
     */
    public function requiresTwoFactor(): bool
    {
        if (! config('auth.two_factor_enabled', true)) {
            return false;
        }

        return $this->hasRole('super-admin') || $this->hasRole('finance');
    }

    /**
     * Where a successful login should land — Reseller accounts don't get the internal
     * dashboard.view permission (it shows staff login history), so they land on their
     * own portal instead.
     */
    public function landingRouteName(): string
    {
        return $this->hasRole('reseller') ? 'reseller-portal.index' : 'dashboard';
    }
}
