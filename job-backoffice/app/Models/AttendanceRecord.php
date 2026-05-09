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
}
