<?php

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('supports company, department, and employee lifecycle endpoints', function () {
    $owner = User::create([
        'name' => 'Owner',
        'email' => 'owner-flow@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $token = postJson('/api/auth/login', [
        'email' => 'owner-flow@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $company = postJson('/api/companies', [
            'name' => 'Flow Co',
            'address' => 'HQ',
            'industry' => 'Tech',
            'website' => 'https://flow.test',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertCreated()
        ->json('data');

    $department = postJson('/api/departments', [
            'company_id' => $company['id'],
            'name' => 'Engineering',
            'code' => 'ENG',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertCreated()
        ->json('data');

    $otherOwner = User::create([
        'name' => 'Other Owner',
        'email' => 'other-owner@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $otherCompany = Company::create([
        'name' => 'Other Co',
        'address' => 'Other HQ',
        'industry' => 'Other',
        'website' => 'https://other-flow.test',
        'ownerId' => $otherOwner->id,
    ]);

    $otherDepartment = Department::create([
        'company_id' => $otherCompany->id,
        'name' => 'Other Department',
        'code' => 'OD',
    ]);

    $employeeUser = User::create([
        'name' => 'Staff',
        'email' => 'staff@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $employee = postJson('/api/employees', [
            'user_id' => $employeeUser->id,
            'department_id' => $department['id'],
            'employee_number' => 'EMP-FLOW-001',
            'job_title' => 'Backend Developer',
            'status' => 'active',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertCreated()
        ->json('data');

    expect($employee['company_id'])->toBe($company['id']);
    expect(Employee::where('user_id', $employeeUser->id)->count())->toBe(1);

    $managerUser = User::create([
        'name' => 'Manager',
        'email' => 'manager@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $manager = postJson('/api/employees', [
            'user_id' => $managerUser->id,
            'department_id' => $department['id'],
            'employee_number' => 'MGR-FLOW-001',
            'job_title' => 'Engineering Manager',
            'status' => 'active',
            'role' => 'manager',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertCreated()
        ->json('data');

    expect($manager['company_id'])->toBe($company['id']);
    expect(User::find($managerUser->id)->hasRole('manager'))->toBeTrue();
    expect(Employee::where('user_id', $managerUser->id)->count())->toBe(1);

    Employee::create([
        'user_id' => null,
        'company_id' => $otherCompany->id,
        'department_id' => $otherDepartment->id,
        'employee_number' => 'EMP-OTHER-001',
        'job_title' => 'Other Employee',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    getJson('/api/employees?company_id='.$otherCompany->id, ['Authorization' => 'Bearer '.$token])
        ->assertOk()
        ->assertJsonCount(2, 'data');

    getJson('/api/departments?company_id='.$otherCompany->id, ['Authorization' => 'Bearer '.$token])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $department['id']);

    getJson('/api/employees/'.$employee['id'], ['Authorization' => 'Bearer '.$token])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $employee['id']);

    putJson('/api/employees/'.$employee['id'], [
            'job_title' => 'Senior Backend Developer',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.job_title', 'Senior Backend Developer');

    postJson('/api/employees/'.$employee['id'].'/terminate', [], ['Authorization' => 'Bearer '.$token])
        ->assertOk()
        ->assertJsonPath('data.status', 'terminated');

    expect(Employee::where('id', $employee['id'])->value('status'))->toBe('terminated');
});
