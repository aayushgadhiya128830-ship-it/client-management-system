<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUSES        = [self::STATUS_ACTIVE, self::STATUS_INACTIVE];

    protected $table = 'clients';

    /** Only these attributes can be mass-assigned (id and timestamps cannot). */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'city',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Search by name, e-mail or phone (partial, case-insensitive on the
     * default MySQL collation).
     *
     * - the term is trimmed; an empty term returns the query unchanged
     * - LIKE wildcards typed by the user (% and _) are escaped so they are
     *   treated as plain characters
     * - the user value is always sent as a bound parameter (no SQL interpolation);
     *   the column names and the ESCAPE character are fixed literals
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term) . '%';

        return $query->where(function (Builder $w) use ($like) {
            $w->whereRaw("name LIKE ? ESCAPE '!'", [$like])
              ->orWhereRaw("email LIKE ? ESCAPE '!'", [$like])
              ->orWhereRaw("phone LIKE ? ESCAPE '!'", [$like]);
        });
    }

    /** Initials (max two letters) used for the avatar circle. */
    public function getInitialsAttribute(): string
    {
        return Str::of($this->name)
            ->trim()
            ->explode(' ')
            ->filter()
            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
