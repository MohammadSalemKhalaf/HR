<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Employee
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $company_id
 * @property string|null $department_id
 * @property string|null $employee_number
 * @property string|null $job_title
 * @property string|null $salary
 * @property \Illuminate\Support\Carbon|null $hired_at
 * @property string|null $status
 * @property string|null $manager_id
 */
class Employee extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'employees';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'employee_number',
        'job_title',
        'salary',
        'hired_at',
        'status',
        'manager_id',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hired_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }

    /**
     * Safely convert/update a user to employee status.
     * Handles both scenarios:
     * - Scenario A: Same user applying to multiple jobs in same/different company -> updates existing employee
     * - Scenario B: Existing employee transitioning to another company -> updates company_id while preserving user/employee
     *
     * Idempotent: Multiple calls with same user result in single employee record, no duplicates.
     *
     * @param User $user The user to convert to employee
     * @param array $attributes Employee attributes (company_id, department_id, job_title, etc)
     * @param string $roleSlug Role to assign (default: 'employee')
     * @return self
     */
    public static function syncForUser(User $user, array $attributes, string $roleSlug = 'employee'): self
    {
        $user->forceFill(['role_id' => User::roleIdFor($roleSlug)])->save();

        return static::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($attributes, [
                'status' => $attributes['status'] ?? 'active',
                'hired_at' => $attributes['hired_at'] ?? now(),
            ])
        );
    }

    public function assignedTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'employee_id');
    }

    public function managedTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'manager_employee_id');
    }
}
