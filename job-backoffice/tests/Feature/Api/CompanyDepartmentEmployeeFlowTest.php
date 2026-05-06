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
        'role' => 'company_owner',
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

    $employeeUser = User::create([
        'name' => 'Staff',
        'email' => 'staff@example.test',
        'password' => bcrypt('password123'),
        'role' => 'job_seeker',
    ]);

    $employee = postJson('/api/employees', [
            'user_id' => $employeeUser->id,
            'company_id' => $company['id'],
            'department_id' => $department['id'],
            'employee_number' => 'EMP-FLOW-001',
            'job_title' => 'Backend Developer',
            'status' => 'active',
        ], ['Authorization' => 'Bearer '.$token])
        ->assertCreated()
        ->json('data');

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
