<?php

use App\Mail\DailyEmployeeReportMail;
use App\Mail\DepartmentBroadcastMail;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Notifications\DailyEmployeeReportNotification;
use App\Notifications\DepartmentBroadcastNotification;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

function managerNotificationFixture(): array
{
    $companyOwner = User::create([
        'name' => 'Company Owner',
        'email' => 'company-owner@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $company = Company::create([
        'name' => 'Broadcast Co',
        'address' => 'HQ',
        'industry' => 'Technology',
        'website' => 'https://broadcast.example.test',
        'ownerId' => $companyOwner->id,
    ]);

    $managerUser = User::create([
        'name' => 'Department Manager',
        'email' => 'manager-broadcast@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('manager'),
    ]);

    $managerEmployee = Employee::create([
        'user_id' => $managerUser->id,
        'company_id' => $company->id,
        'department_id' => null,
        'job_title' => 'Department Manager',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    $department = Department::create([
        'company_id' => $company->id,
        'name' => 'Engineering',
        'code' => 'ENG',
        'manager_employee_id' => $managerEmployee->id,
    ]);

    $managerEmployee->update(['department_id' => $department->id]);

    $employeeOneUser = User::create([
        'name' => 'Employee One',
        'email' => 'employee-one@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('employee'),
    ]);

    $employeeOne = Employee::create([
        'user_id' => $employeeOneUser->id,
        'company_id' => $company->id,
        'department_id' => $department->id,
        'job_title' => 'Developer',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => $managerEmployee->id,
    ]);

    $employeeTwoUser = User::create([
        'name' => 'Employee Two',
        'email' => 'employee-two@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('employee'),
    ]);

    $employeeTwo = Employee::create([
        'user_id' => $employeeTwoUser->id,
        'company_id' => $company->id,
        'department_id' => $department->id,
        'job_title' => 'Developer',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => $managerEmployee->id,
    ]);

    $otherDepartment = Department::create([
        'company_id' => $company->id,
        'name' => 'Sales',
        'code' => 'SAL',
        'manager_employee_id' => null,
    ]);

    $otherEmployeeUser = User::create([
        'name' => 'Other Employee',
        'email' => 'other-employee@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('employee'),
    ]);

    $otherEmployee = Employee::create([
        'user_id' => $otherEmployeeUser->id,
        'company_id' => $company->id,
        'department_id' => $otherDepartment->id,
        'job_title' => 'Sales Rep',
        'hired_at' => now(),
        'status' => 'active',
        'manager_id' => null,
    ]);

    return compact(
        'company',
        'managerUser',
        'managerEmployee',
        'department',
        'employeeOne',
        'employeeTwo',
        'otherDepartment',
        'otherEmployee'
    );
}

it('loads the department notifications page for managers', function () {
    $fixture = managerNotificationFixture();

    actingAs($fixture['managerUser'])
        ->get(route('manager.department-notifications.index'))
        ->assertOk()
        ->assertSee('Department Notifications')
        ->assertSee($fixture['department']->name);
});

it('sends a department broadcast to the whole department and records notifications', function () {
    $fixture = managerNotificationFixture();

    Mail::fake();

    actingAs($fixture['managerUser'])
        ->post(route('manager.department-notifications.store'), [
            'department_id' => $fixture['department']->id,
            'recipient_mode' => 'all',
            'type' => 'meeting',
            'title' => 'Meeting tomorrow at 10 AM',
            'message' => 'Please attend the department meeting in the main conference room.',
        ])
        ->assertRedirect(route('manager.department-notifications.index', ['department_id' => $fixture['department']->id]))
        ->assertSessionHas('success');

    $notification = new DepartmentBroadcastNotification(
        $fixture['managerEmployee'],
        $fixture['department'],
        [
            'type' => 'meeting',
            'type_label' => 'Meeting',
            'title' => 'Meeting tomorrow at 10 AM',
            'message' => 'Please attend the department meeting in the main conference room.',
            'recipient_mode' => 'all',
            'recipient_count' => 2,
        ]
    );

    expect($notification->toMail($fixture['employeeOne']->user))->toBeInstanceOf(DepartmentBroadcastMail::class);

    expect(DatabaseNotification::where('type', DepartmentBroadcastNotification::class)->count())->toBe(2);
    expect(DatabaseNotification::where('notifiable_id', $fixture['employeeOne']->user_id)->where('type', DepartmentBroadcastNotification::class)->count())->toBe(1);
    expect(DatabaseNotification::where('notifiable_id', $fixture['employeeTwo']->user_id)->where('type', DepartmentBroadcastNotification::class)->count())->toBe(1);
});

it('sends a department broadcast only to the selected employee', function () {
    $fixture = managerNotificationFixture();

    Mail::fake();

    actingAs($fixture['managerUser'])
        ->post(route('manager.department-notifications.store'), [
            'department_id' => $fixture['department']->id,
            'recipient_mode' => 'specific',
            'type' => 'warning',
            'title' => 'Performance warning',
            'message' => 'Please improve task completion this week.',
            'employee_ids' => [$fixture['employeeOne']->id],
        ])
        ->assertRedirect();

    $notification = new DepartmentBroadcastNotification(
        $fixture['managerEmployee'],
        $fixture['department'],
        [
            'type' => 'warning',
            'type_label' => 'Warning',
            'title' => 'Performance warning',
            'message' => 'Please improve task completion this week.',
            'recipient_mode' => 'specific',
            'recipient_count' => 1,
        ]
    );

    expect($notification->toMail($fixture['employeeOne']->user))->toBeInstanceOf(DepartmentBroadcastMail::class);

    expect(DatabaseNotification::where('type', DepartmentBroadcastNotification::class)->count())->toBe(1);
    expect(DatabaseNotification::where('notifiable_id', $fixture['employeeOne']->user_id)->where('type', DepartmentBroadcastNotification::class)->count())->toBe(1);
    expect(DatabaseNotification::where('notifiable_id', $fixture['employeeTwo']->user_id)->where('type', DepartmentBroadcastNotification::class)->count())->toBe(0);
});

it('rejects employees from outside the managed department', function () {
    $fixture = managerNotificationFixture();

    actingAs($fixture['managerUser'])
        ->post(route('manager.department-notifications.store'), [
            'department_id' => $fixture['department']->id,
            'recipient_mode' => 'specific',
            'type' => 'general_announcement',
            'title' => 'Invalid target',
            'message' => 'This should fail.',
            'employee_ids' => [$fixture['otherEmployee']->id],
        ])
        ->assertSessionHasErrors('employee_ids.0');
});

it('sends a daily employee report to the manager after checkout', function () {
    $fixture = managerNotificationFixture();

    $employee = $fixture['employeeOne'];
    $managerUser = $fixture['managerUser'];

    EmployeeTask::create([
        'company_id' => $fixture['company']->id,
        'department_id' => $fixture['department']->id,
        'manager_employee_id' => $fixture['managerEmployee']->id,
        'employee_id' => $employee->id,
        'title' => 'Completed Task 1',
        'description' => 'Finished task',
        'priority' => 'medium',
        'status' => 'completed',
        'completed_at' => now()->subHour(),
    ]);

    EmployeeTask::create([
        'company_id' => $fixture['company']->id,
        'department_id' => $fixture['department']->id,
        'manager_employee_id' => $fixture['managerEmployee']->id,
        'employee_id' => $employee->id,
        'title' => 'Pending Task 1',
        'description' => 'Still pending',
        'priority' => 'high',
        'status' => 'in_progress',
    ]);

    AttendanceRecord::create([
        'employee_id' => $employee->id,
        'attendance_date' => now()->toDateString(),
        'check_in_at' => Carbon::parse(now()->toDateString() . ' 09:15:00'),
        'status' => 'present',
    ]);

    Mail::fake();

    $request = Request::create('/employee/attendance/check-out', 'POST');
    $request->setUserResolver(fn () => $employee->user);

    $response = app(\App\Http\Controllers\EmployeeArea\AttendanceController::class)->checkOut($request);

    expect($response->getStatusCode())->toBe(302);

    $record = AttendanceRecord::where('employee_id', $employee->id)
        ->latest()
        ->firstOrFail();

    $builtNotification = new DailyEmployeeReportNotification(
        $employee,
        $record->fresh(),
        [
            'employee_name' => 'Employee One',
            'company_name' => $fixture['company']->name,
            'department_name' => $fixture['department']->name,
            'date' => now()->toDateString(),
            'check_in_time' => '09:15 AM',
            'check_out_time' => now()->format('h:i A'),
            'worked_hours' => '8h 0m',
            'late_label' => '15 minutes',
            'summary_line' => 'Completed 1 out of 2 tasks today',
            'attendance_status' => 'Late',
            'completed_task_titles' => ['Completed Task 1'],
        ],
        $managerUser
    );

    expect($builtNotification->toMail($managerUser))->toBeInstanceOf(DailyEmployeeReportMail::class);

    \Illuminate\Support\Facades\Notification::send($managerUser, $builtNotification);

    $notification = DatabaseNotification::latest()->first();

    expect($notification)->not->toBeNull();
    expect($notification->notifiable_id)->toBe($managerUser->id);
    expect($notification->type)->toBe(DailyEmployeeReportNotification::class);
    expect($notification->data['employee_name'])->toBe('Employee One');
    expect($notification->data['summary_line'])->toContain('Completed 1 out of 2 tasks today');
});

it('blocks employees from accessing manager department notifications', function () {
    $fixture = managerNotificationFixture();

    actingAs($fixture['employeeOne']->user)
        ->get(route('manager.department-notifications.index'))
        ->assertForbidden();
});
