<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'leaves';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'status',
        'requested_by_user_id',
        'approved_by_user_id',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    // Accessor for type (alias for leave_type)
    public function getTypeAttribute()
    {
        return $this->leave_type;
    }
}
