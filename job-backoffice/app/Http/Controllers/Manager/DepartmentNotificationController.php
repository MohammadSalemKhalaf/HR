<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentNotificationRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Notifications\DepartmentBroadcastNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;

class DepartmentNotificationController extends Controller
{
    private function managerEmployee(Request $request): Employee
    {
        return Employee::where('user_id', $request->user()->id)->firstOrFail();
    }

    private function managedDepartments(Employee $managerEmployee)
    {
        return Department::where('manager_employee_id', $managerEmployee->id)
            ->orderBy('name')
            ->get();
    }

    public function index(Request $request)
    {
        $managerEmployee = $this->managerEmployee($request);
        $departments = $this->managedDepartments($managerEmployee);

        $selectedDepartmentId = old('department_id', $request->string('department_id')->toString() ?: $departments->first()?->id);
        $selectedDepartment = $selectedDepartmentId
            ? $departments->firstWhere('id', $selectedDepartmentId)
            : null;

        if (! $selectedDepartment && $departments->isNotEmpty()) {
            $selectedDepartment = $departments->first();
            $selectedDepartmentId = $selectedDepartment->id;
        }

        $departmentEmployees = Employee::with('user')
            ->where('company_id', $managerEmployee->company_id)
            ->where('id', '!=', $managerEmployee->id)
            ->whereNotNull('user_id')
            ->whereIn('department_id', $departments->pluck('id'))
            ->orderBy('department_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('department_id')
            ->map(function ($employees) {
                return $employees->map(fn (Employee $employee) => [
                    'id' => $employee->id,
                    'name' => $employee->user?->name ?? 'Employee',
                    'email' => $employee->user?->email,
                ])->values();
            });

        $notificationTypes = config('ems.department_notifications.types', []);

        return view('manager.department-notifications.index', compact(
            'departments',
            'selectedDepartment',
            'selectedDepartmentId',
            'departmentEmployees',
            'notificationTypes'
        ));
    }

    public function store(DepartmentNotificationRequest $request)
    {
        $managerEmployee = $this->managerEmployee($request);
        $validated = $request->validated();

        $department = Department::where('id', $validated['department_id'])
            ->where('company_id', $managerEmployee->company_id)
            ->where('manager_employee_id', $managerEmployee->id)
            ->firstOrFail();

        $typeLabel = data_get(config('ems.department_notifications.types', []), $validated['type'], 'General Announcement');

        $employeeQuery = Employee::with('user')
            ->where('company_id', $managerEmployee->company_id)
            ->where('id', '!=', $managerEmployee->id)
            ->where('department_id', $department->id)
            ->whereNotNull('user_id');

        if (($validated['recipient_mode'] ?? 'all') === 'specific') {
            $employeeIds = collect($validated['employee_ids'] ?? [])->unique()->values();
            $employeeQuery->whereIn('id', $employeeIds->all());
        }

        $recipients = $employeeQuery->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return Redirect::back()->withInput()->withErrors([
                'employee_ids' => 'No valid employees were found for the selected department.',
            ]);
        }

        $payload = [
            'type' => $validated['type'],
            'type_label' => $typeLabel,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'department_id' => $department->id,
            'department_name' => $department->name,
            'recipient_mode' => $validated['recipient_mode'],
            'recipient_count' => $recipients->count(),
            'sender_name' => $managerEmployee->user?->name,
        ];

        try {
            Notification::send($recipients, new DepartmentBroadcastNotification($managerEmployee, $department, $payload));
        } catch (\Throwable $e) {
            report($e);
            return Redirect::back()->withInput()->with('error', 'Notification queued could not be sent. Please retry.');
        }

        app(ActivityLogService::class)->log(
            $managerEmployee->company_id,
            $request->user()->id,
            'department.notification.sent',
            "Department notification sent: {$validated['title']}",
            $department,
            ['recipient_mode' => $validated['recipient_mode'], 'recipient_count' => $recipients->count()]
        );

        return Redirect::route('manager.department-notifications.index', ['department_id' => $department->id])
            ->with('success', 'Department notification sent successfully.');
    }
}
