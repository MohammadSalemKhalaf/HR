<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'roles';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'slug',
        'name',
    ];

    public static function normalizeSlug(mixed $slug): ?string
    {
        if ($slug === null) {
            return null;
        }

        if (is_object($slug) && method_exists($slug, '__toString')) {
            $slug = (string) $slug;
        }

        if (! is_string($slug)) {
            return null;
        }

        return match ($slug) {
            'company_owner' => 'company',
            default => $slug,
        };
    }

    public static function idForSlug(mixed $slug): ?string
    {
        $normalizedSlug = static::normalizeSlug($slug);

        if ($normalizedSlug === null || $normalizedSlug === '') {
            return null;
        }

        return static::query()->where('slug', $normalizedSlug)->value('id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}