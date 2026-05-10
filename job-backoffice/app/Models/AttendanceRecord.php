<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AttendanceRecord
 *
 * @property string $id
 * @property string $employee_id
 * @property \Illuminate\Support\Carbon $attendance_date
 * @property \Illuminate\Support\Carbon|null $check_in_at
 * @property \Illuminate\Support\Carbon|null $check_out_at
 * @property string|null $status
 * @property string|null $notes
 */
class AttendanceRecord extends Model
{
    use HasUuids;

    protected $table = 'attendance_records';

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    protected $appends = [
        'hours',
    ];

    // Explicit constructor to satisfy static analyzers which may warn when
    // models are instantiated with attribute arrays.
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Human-readable worked hours between check-in and check-out.
     * Returns null if either timestamp is missing.
     * Examples: "8h 15m", "2h", "30m"
     */
    public function getHoursAttribute(): ?string
    {
        if (! $this->check_in_at || ! $this->check_out_at) {
            return null;
        }

        $minutes = $this->check_in_at->diffInMinutes($this->check_out_at);

        if ($minutes <= 0) return null;

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        }

        if ($hours > 0) {
            return "{$hours}h";
        }

        return "{$mins}m";
    }
}
