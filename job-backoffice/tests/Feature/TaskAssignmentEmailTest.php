<?php

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\postJson;

function taskAssignmentFixture(): array
{
    $ownerUser = User::create([
        'name' => 'Company Owner',
        'email' => 'owner-task@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $company = Company::create([
        'name' => 'Task Co',
        'address' => 'HQ',
        'industry' => 'Technology',
        'website' => 'https://task.example.test',
        'ownerId' => $ownerUser->id,
    ]);

    $managerUser = User::create([
        'name' => 'Manager User',
        'email' => 'manager-task@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('manager'),
    ]);

    $managerEmployee = Employee::create([
        'user_id' => $managerUser->id,
        'company_id' => $company->id,
        'department_id' => null,
        'job_title' => 'Engineering Manager',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    $department = Department::create([
        'company_id' => $company->id,
        'name' => 'Engineering',
        'code' => 'ENG-TASK',
        'manager_employee_id' => $managerEmployee->id,
    ]);

    $managerEmployee->update(['department_id' => $department->id]);

    $employeeUser = User::create([
        'name' => 'Aseel Employee',
        'email' => 'kaseel134@gmail.com',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('employee'),
    ]);

    $employee = Employee::create([
        'user_id' => $employeeUser->id,
        'company_id' => $company->id,
        'department_id' => $department->id,
        'job_title' => 'Software Engineer',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => $managerEmployee->id,
    ]);

    return compact('company', 'ownerUser', 'managerUser', 'managerEmployee', 'department', 'employeeUser', 'employee');
}

it('sends task assignments by mail and database to the employee user', function () {
    $fixture = taskAssignmentFixture();

    Notification::fake();

    $managerToken = postJson('/api/auth/login', [
        'email' => $fixture['managerUser']->email,
        'password' => 'password123',
    ])->json('data.token');

    postJson('/api/manager/tasks', [
            'title' => 'Prepare weekly report',
            'description' => 'Compile the weekly delivery metrics.',
            'repository_url' => 'https://github.com/example/repo',
            'priority' => 'high',
            'employee_id' => $fixture['employee']->id,
            'due_date' => '2026-05-15',
        ], ['Authorization' => 'Bearer '.$managerToken])
        ->assertCreated()
        ->assertJsonPath('message', 'Task created successfully.');

    Notification::assertSentTo(
        $fixture['employeeUser'],
        TaskAssigned::class,
        function (TaskAssigned $notification, array $channels) use ($fixture) {
            $databasePayload = $notification->toDatabase($fixture['employeeUser']);
            $mailMessage = $notification->toMail($fixture['employeeUser']);

            return in_array('database', $channels, true)
                && in_array('mail', $channels, true)
                && $databasePayload['title'] === 'Prepare weekly report'
                && $databasePayload['due_date'] === '2026-05-15'
                && $mailMessage instanceof Mailable;
        }
    );
});