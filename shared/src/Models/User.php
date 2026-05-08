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
        'role',
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

    public function getRoleAttribute(): ?string
    {
        return Role::normalizeSlug($this->assignedRole?->slug ?? $this->attributes['role'] ?? null);
    }

    public function setRoleAttribute($value): void
    {
        $normalizedRole = Role::normalizeSlug($value);

        $this->attributes['role'] = $normalizedRole;
        $this->attributes['role_id'] = $normalizedRole ? Role::idForSlug($normalizedRole) : null;
    }

    public function setRoleIdAttribute($value): void
    {
        $this->attributes['role_id'] = $value;

        if ($value) {
            $role = Role::query()->find($value);

            if ($role) {
                $this->attributes['role'] = $role->slug;
            }
        }
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
