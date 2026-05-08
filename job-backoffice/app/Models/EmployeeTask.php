<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EmployeeTask extends Model
{
    use HasUuids;

    protected $table = 'employee_tasks';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'company_id', 'department_id', 'manager_employee_id', 'employee_id',
        'title', 'description', 'repository_url', 'priority', 'status', 'due_date', 'completed_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
