<?php

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('creates, approves, rejects, and lists employee leaves', function () {
    $owner = User::create([
        'name' => 'Leave Owner',
        'email' => 'leave-owner@example.test',
        'password' => bcrypt('password123'),
        'role' => 'company',
    ]);

    $ownerToken = postJson('/api/auth/login', [
        'email' => 'leave-owner@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $company = postJson('/api/companies', [
            'name' => 'Leave Co',
            'address' => 'HQ',
            'industry' => 'Services',
            'website' => 'https://leave.test',
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->json('data');

    $department = postJson('/api/departments', [
            'company_id' => $company['id'],
            'name' => 'Operations',
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->json('data');

    $employeeUser = User::create([
        'name' => 'Employee Leave',
        'email' => 'employee-leave@example.test',
        'password' => bcrypt('password123'),
        'role' => 'job_seeker',
    ]);

    $employee = Employee::create([
        'user_id' => $employeeUser->id,
        'company_id' => $company['id'],
        'department_id' => $department['id'],
        'employee_number' => 'EMP-LEAVE-001',
        'job_title' => 'Analyst',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    $employeeToken = postJson('/api/auth/login', [
        'email' => 'employee-leave@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $leave = postJson('/api/leave/apply', [
            'employee_id' => $employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ], ['Authorization' => 'Bearer '.$employeeToken])
        ->assertCreated()
        ->json('data');

    postJson('/api/leave/approve', ['leave_id' => $leave['id']], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertOk()
        ->assertJsonPath('data.status', 'approved');

    postJson('/api/leave/reject', ['leave_id' => $leave['id']], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertOk()
        ->assertJsonPath('data.status', 'rejected');

    getJson('/api/leaves?employee_id='.$employee->id, ['Authorization' => 'Bearer '.$employeeToken])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(Leave::where('employee_id', $employee->id)->count())->toBe(1);
});
