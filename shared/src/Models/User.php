<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $role_id
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'last_login_at',
        'password',
        'role_id',
    ];
    protected $dates = [
        'deleted_at',
    ];

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
            'deleted_at'=> 'datetime',
        ];
    }

    public static function roleIdFor(mixed $roleSlug): ?string
    {
        return Role::idForSlug($roleSlug);
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the role attribute.
     * Reads from role_id and roles table relationship (source of truth).
     * Never reads from the legacy 'role' column anymore.
     *
     * @return string|null
     */
    public function getRoleAttribute(): ?string
    {
        return Role::normalizeSlug($this->assignedRole?->slug ?? null);
    }

    /**
     * DEPRECATED - Maintained for backward compatibility during migration.
     * Setting role as a string converts it to role_id.
     * Use role_id directly instead.
     *
     * @param mixed $value
     * @return void
     */
    public function setRoleAttribute($value): void
    {
        // Convert string role to role_id for backward compatibility
        if (is_string($value)) {
            $normalizedSlug = Role::normalizeSlug($value);
            if ($normalizedSlug) {
                $this->attributes['role_id'] = Role::idForSlug($normalizedSlug);
            }
        }
    }

    public function setRoleIdAttribute($value): void
    {
        $this->attributes['role_id'] = $value;
        
        // Do NOT write to legacy 'role' column during role_id updates.
        // The getter (getRoleAttribute) handles reading from role_id first.
        // Skipping the write prevents truncation errors when legacy column
        // has different enum constraints or stale/invalid values.
    }

    public function hasRole(string|array $roles): bool
    {
        $currentRole = $this->getRoleAttribute();
        $roles = array_map(static fn (string $role) => Role::normalizeSlug($role), is_array($roles) ? $roles : [$roles]);

        return in_array($currentRole, $roles, true);
    }

    public function roleSlug(): ?string
    {
        return $this->getRoleAttribute();
    }

    public function roleName(): ?string
    {
        return $this->assignedRole?->name;
    }
    
     public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class,'userId','id');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class,'userId','id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class,'ownerId','id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }
}
