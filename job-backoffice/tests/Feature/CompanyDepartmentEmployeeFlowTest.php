<?php

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

function companyUserWithCompany(string $email, string $name): array
{
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $company = Company::create([
        'name' => $name.' Co',
        'address' => 'HQ',
        'industry' => 'Tech',
        'website' => 'https://example.test',
        'ownerId' => $user->id,
    ]);

    return [$user, $company];
}

function companyEmployee(Company $company, string $email, string $name, string $roleSlug = 'employee', string $status = 'active'): Employee
{
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor($roleSlug),
    ]);

    return Employee::create([
        'user_id' => $user->id,
        'company_id' => $company->id,
        'department_id' => null,
        'job_title' => $roleSlug === 'manager' ? 'Manager' : 'Staff',
        'hired_at' => now(),
        'status' => $status,
        'manager_id' => null,
    ]);
}

it('handles company department crud with manager validation and company employee access', function () {
    [$companyUser, $company] = companyUserWithCompany('company-flow@example.test', 'Company Flow');
    actingAs($companyUser);

    $manager = companyEmployee($company, 'manager-flow@example.test', 'Manager Flow', 'manager');
    $normalEmployee = companyEmployee($company, 'employee-flow@example.test', 'Employee Flow', 'employee');

    get(route('departments.create'))
        ->assertOk()
        ->assertSee($manager->user->name)
        ->assertDontSee($normalEmployee->user->name);

    post(route('departments.store'), [
        'name' => 'Operations',
        'code' => 'OPS',
        'manager_employee_id' => null,
    ])
        ->assertRedirect();

    $department = Department::where('company_id', $company->id)->where('name', 'Operations')->firstOrFail();
    expect($department->manager_employee_id)->toBeNull();

    post(route('departments.store'), [
        'name' => 'Engineering',
        'code' => 'ENG',
        'manager_employee_id' => $manager->id,
    ])
        ->assertRedirect();

    $managedDepartment = Department::where('company_id', $company->id)->where('name', 'Engineering')->firstOrFail();
    expect($managedDepartment->manager_employee_id)->toBe($manager->id);

    post(route('departments.store'), [
        'name' => 'Invalid Manager Department',
        'code' => 'BAD',
        'manager_employee_id' => $normalEmployee->id,
    ])->assertSessionHasErrors('manager_employee_id');

    get(route('departments.show', $managedDepartment->id))->assertOk();
    get(route('departments.edit', $managedDepartment->id))
        ->assertOk()
        ->assertSee($manager->user->name)
        ->assertDontSee($normalEmployee->user->name);

    put(route('departments.update', $managedDepartment->id), [
        'name' => 'Engineering Updated',
        'code' => 'ENG2',
        'manager_employee_id' => $manager->id,
    ])->assertRedirect();

    expect(Department::find($managedDepartment->id)->name)->toBe('Engineering Updated');

    put(route('departments.update', $managedDepartment->id), [
        'name' => 'Engineering Invalid',
        'code' => 'ENG3',
        'manager_employee_id' => $normalEmployee->id,
    ])->assertSessionHasErrors('manager_employee_id');

    $deleteTarget = Department::create([
        'company_id' => $company->id,
        'name' => 'Temporary',
        'code' => 'TMP',
        'manager_employee_id' => null,
    ]);

    delete(route('departments.destroy', $deleteTarget->id))->assertRedirect(route('departments.index'));
    expect(Department::find($deleteTarget->id))->toBeNull();
});

it('handles company employee create view show edit and role changes without forbidden redirects', function () {
    [$companyUser, $company] = companyUserWithCompany('company-employee@example.test', 'Company Employee');
    actingAs($companyUser);

    $department = Department::create([
        'company_id' => $company->id,
        'name' => 'Operations',
        'code' => 'OPS',
        'manager_employee_id' => null,
    ]);

    $employeeUser = User::create([
        'name' => 'New Hire',
        'email' => 'new-hire@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $createdEmployeeId = Employee::syncForUser($employeeUser, [
        'company_id' => $company->id,
        'department_id' => $department->id,
        'job_title' => 'Analyst',
        'salary' => '5000',
        'hired_at' => now(),
        'status' => 'active',
    ], 'employee')->id;

    get(route('company-employees.create'))->assertOk();

    expect(Employee::find($createdEmployeeId)->company_id)->toBe($company->id);
    expect((float) Employee::find($createdEmployeeId)->salary)->toBe(5000.0);
    actingAs($companyUser)->get(route('company-employees.show', $createdEmployeeId))->assertOk();
    actingAs($companyUser)->get(route('company-employees.edit', $createdEmployeeId))->assertOk();

    actingAs($companyUser)->put(route('company-employees.update', $createdEmployeeId), [
        'name' => 'New Hire Updated',
        'job_title' => 'Senior Analyst',
        'department_id' => $department->id,
        'role_type' => 'manager',
        'salary' => '6000',
        'hired_at' => now()->toDateString(),
        'status' => 'active',
    ])->assertRedirect();

    expect(User::find($employeeUser->id)->hasRole('manager'))->toBeTrue();
    expect((float) Employee::find($createdEmployeeId)->salary)->toBe(6000.0);

    actingAs($companyUser)->put(route('company-employees.update', $createdEmployeeId), [
        'name' => 'New Hire Downgraded',
        'job_title' => 'Analyst',
        'department_id' => $department->id,
        'role_type' => 'employee',
        'salary' => '5500',
        'hired_at' => now()->toDateString(),
        'status' => 'active',
    ])->assertRedirect();

    expect(User::find($employeeUser->id)->hasRole('employee'))->toBeTrue();
    expect((float) Employee::find($createdEmployeeId)->salary)->toBe(5500.0);

    $managerUser = User::create([
        'name' => 'Department Manager',
        'email' => 'department-manager@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('manager'),
    ]);

    $managerEmployee = Employee::create([
        'user_id' => $managerUser->id,
        'company_id' => $company->id,
        'department_id' => $department->id,
        'job_title' => 'Manager',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    $department->update(['manager_employee_id' => $managerEmployee->id]);

    actingAs($companyUser)->put(route('company-employees.update', $managerEmployee->id), [
        'name' => 'Department Manager Downgraded',
        'job_title' => 'Staff',
        'department_id' => $department->id,
        'role_type' => 'employee',
        'hired_at' => now()->toDateString(),
        'status' => 'active',
    ])->assertSessionHasErrors('role_type');

    expect(User::find($managerUser->id)->hasRole('manager'))->toBeTrue();
    expect(Department::find($department->id)->manager_employee_id)->toBe($managerEmployee->id);

    actingAs($companyUser)->delete(route('company-employees.destroy', $createdEmployeeId))->assertRedirect(route('company-employees.index'));
    expect(Employee::find($createdEmployeeId))->toBeNull();
    expect(Employee::withTrashed()->find($createdEmployeeId))->not->toBeNull();

    actingAs($companyUser)->post(route('company-employees.store'), [
        'name' => 'Inline Hire',
        'email' => 'inline-hire@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role_type' => 'employee',
        'department_id' => $department->id,
        'job_title' => 'Coordinator',
        'salary' => '3500',
        'hired_at' => now()->toDateString(),
    ])->assertRedirect();

    $inlineUser = User::where('email', 'inline-hire@example.test')->firstOrFail();
    $inlineEmployee = Employee::where('user_id', $inlineUser->id)->firstOrFail();

    expect($inlineEmployee->company_id)->toBe($company->id);
    expect((float) $inlineEmployee->salary)->toBe(3500.0);
    expect($companyUser->fresh()->company?->id)->toBe($company->id);
});
