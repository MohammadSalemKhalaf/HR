<?php

use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('registers and logs in core roles with token payloads', function (string $role) {
    $email = $role.'@example.test';

    $register = postJson('/api/auth/register', [
        'name' => ucfirst($role).' User',
        'email' => $email,
        'password' => 'password123',
        'role' => $role,
    ]);

    $register->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.role', $role)
        ->assertJsonStructure(['success', 'message', 'data' => ['token', 'user_id', 'role']]);

    $login = postJson('/api/auth/login', [
        'email' => $email,
        'password' => 'password123',
    ]);

    $login->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.role', $role);
})->with(['admin', 'company', 'job_seeker']);

it('logs in an employee using the derived employee role', function () {
    $owner = User::create([
        'name' => 'Owner',
        'email' => 'owner@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $company = Company::create([
        'name' => 'Acme Inc',
        'address' => 'Main Street',
        'industry' => 'Technology',
        'website' => 'https://acme.test',
        'ownerId' => $owner->id,
    ]);

    $employeeUser = User::create([
        'name' => 'Employee',
        'email' => 'employee@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $employee = Employee::create([
        'user_id' => $employeeUser->id,
        'company_id' => $company->id,
        'department_id' => null,
        'employee_number' => 'EMP-001',
        'job_title' => 'Developer',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    $response = postJson('/api/auth/login', [
        'email' => 'employee@example.test',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.role', 'employee')
        ->assertJsonPath('data.company_id', $company->id)
        ->assertJsonPath('data.employee_id', $employee->id);
});

it('logs in a manager using the company login flow', function () {
    $manager = User::create([
        'name' => 'Manager',
        'email' => 'manager-login@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('manager'),
    ]);

    $response = postJson('/api/auth/company/login', [
        'email' => 'manager-login@example.test',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.role', 'manager');
});
