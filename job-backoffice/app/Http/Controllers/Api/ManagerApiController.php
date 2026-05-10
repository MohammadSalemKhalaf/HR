<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentNotificationRequest;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Notifications\DepartmentBroadcastNotification;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ManagerApiController extends Controller
{
    private function managerEmployee(): ?Employee
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first();
    }

    private function managerEmployeeId(): ?string
    {
        return $this->managerEmployee()?->id;
    }

    private function managedDepartmentIds(): array
    {
        $managerId = $this->managerEmployeeId();
        if (!$managerId) return [];
        return Department::where('manager_employee_id', $managerId)
            ->pluck('id')
            ->map(fn ($value) => (string) $value)
            ->toArray();
    }

    // DEPARTMENTS
    public function getDepartments(): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $departments = Department::where('manager_employee_id', $managerId)
            ->with('manager.user')
            ->withCount('employees')
            ->paginate(15);

        return response()->json($departments);
    }

    public function getDepartment(string $id): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $department = Department::where('id', $id)
            ->where('manager_employee_id', $managerId)
            ->with('manager.user')
            ->firstOrFail();

        $employees = Employee::with('user')
            ->where('department_id', $department->id)
            ->where('status', '!=', 'terminated')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'department' => $department,
            'employees' => $employees,
        ]);
    }

    public function getDepartmentEmployees(string $id): JsonResponse
    {
        $manager = $this->managerEmployee();
        if (! $manager) {
            abort(403);
        }

        $department = Department::where('id', $id)
            ->where('company_id', $manager->company_id)
            ->where('manager_employee_id', $manager->id)
            ->firstOrFail();

        $employees = Employee::with('user')
            ->where('company_id', $manager->company_id)
            ->where('id', '!=', $manager->id)
            ->whereNotNull('user_id')
            ->where('department_id', $department->id)
            ->orderBy('created_at')
            ->get()
            ->map(function (Employee $employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->user?->name ?? 'Employee',
                    'email' => $employee->user?->email,
                ];
            })
            ->values();

        return response()->json([
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'employees' => $employees,
        ]);
    }

    // EMPLOYEES
    public function getEmployees(): JsonResponse
    {
        $deptIds = $this->managedDepartmentIds();
        if (empty($deptIds)) {
            return response()->json(['data' => []]);
        }

        $employees = Employee::whereIn('department_id', $deptIds)
            ->where('status', '!=', 'terminated')
            ->with('user', 'department', 'manager.user')
            ->paginate(15);

        return response()->json($employees);
    }

    public function getEmployee(string $id): JsonResponse
    {
        $deptIds = $this->managedDepartmentIds();

        $employee = Employee::with('user', 'department', 'manager.user')
            ->findOrFail($id);

        if (! in_array((string) $employee->department_id, $deptIds, true)) {
            abort(403);
        }

        return response()->json($employee);
    }

    // LEAVES
    public function getLeaves(): JsonResponse
    {
        $deptIds = $this->managedDepartmentIds();
        if (empty($deptIds)) {
            return response()->json(['data' => []]);
        }

        $leaves = Leave::whereIn('employee_id',
            Employee::whereIn('department_id', $deptIds)->pluck('id')
        )
            ->with(['employee.user', 'employee.department', 'requestedBy', 'approvedBy'])
            ->latest()
            ->paginate(15);

        return response()->json($leaves);
    }

    public function approveLeave(string $id): JsonResponse
    {
        $manager = $this->managerEmployee();
        $user = Auth::user();
        if (! $manager || ! $user) {
            abort(403);
        }

        $leave = Leave::with('employee.user')->findOrFail($id);
        $deptIds = $this->managedDepartmentIds();

        if (! in_array((string) $leave->employee?->department_id, $deptIds, true)) {
            abort(403);
        }

        $leave->status = 'approved';
        $leave->approved_by_user_id = $user->id;
        $leave->approved_at = now();
        $leave->save();

        app(ActivityLogService::class)->log(
            $manager->company_id,
            $user->id,
            'leave.approved',
            "Leave approved for {$leave->employee->user?->name}",
            $leave,
            ['leave_id' => $leave->id]
        );

        try {
            if ($leave->employee?->user) {
                Notification::send($leave->employee->user, new LeaveApproved($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Leave approved.',
            'leave' => $leave->fresh(['employee.user', 'employee.department', 'approvedBy']),
        ]);
    }

    public function rejectLeave(string $id): JsonResponse
    {
        $manager = $this->managerEmployee();
        $user = Auth::user();
        if (! $manager || ! $user) {
            abort(403);
        }

        $leave = Leave::with('employee.user')->findOrFail($id);
        $deptIds = $this->managedDepartmentIds();

        if (! in_array((string) $leave->employee?->department_id, $deptIds, true)) {
            abort(403);
        }

        $leave->status = 'rejected';
        $leave->approved_by_user_id = $user->id;
        $leave->approved_at = now();
        $leave->save();

        app(ActivityLogService::class)->log(
            $manager->company_id,
            $user->id,
            'leave.rejected',
            "Leave rejected for {$leave->employee->user?->name}",
            $leave,
            ['leave_id' => $leave->id]
        );

        try {
            if ($leave->employee?->user) {
                Notification::send($leave->employee->user, new LeaveRejected($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Leave rejected.',
            'leave' => $leave->fresh(['employee.user', 'employee.department', 'approvedBy']),
        ]);
    }

    // ATTENDANCE
    public function getAttendance(Request $request): JsonResponse
    {
        $deptIds = $this->managedDepartmentIds();
        if (empty($deptIds)) {
            return response()->json(['data' => []]);
        }

        $date = $request->query('date', now()->toDateString());

        $attendance = AttendanceRecord::whereIn('employee_id',
            Employee::whereIn('department_id', $deptIds)->pluck('id')
        )
            ->where('attendance_date', $date)
            ->with('employee.user', 'employee.department')
            ->latest()
            ->paginate(15);

        return response()->json($attendance);
    }

    // DASHBOARD STATS
    public function getDashboardStats(): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $deptIds = $this->managedDepartmentIds();

        $departmentsCount = count($deptIds);

        $employeesCount = Employee::whereIn('department_id', $deptIds)
            ->where('status', '!=', 'terminated')
            ->count();

        $pendingLeaves = Leave::whereIn('employee_id',
            Employee::whereIn('department_id', $deptIds)->pluck('id')
        )
            ->where('status', 'pending')
            ->count();

        $today = now()->toDateString();
        $todayAttendance = AttendanceRecord::whereIn('employee_id',
            Employee::whereIn('department_id', $deptIds)->pluck('id')
        )
            ->where('attendance_date', $today)
            ->count();

        return response()->json([
            'departmentsCount' => $departmentsCount,
            'employeesCount' => $employeesCount,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
        ]);
    }

    public function getNotificationMeta(Request $request): JsonResponse
    {
        $manager = $this->managerEmployee();
        if (! $manager) {
            abort(403);
        }

        $departments = Department::where('manager_employee_id', $manager->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedDepartmentId = $request->query('department_id') ?: $departments->first()?->id;
        $selectedDepartment = $selectedDepartmentId
            ? $departments->firstWhere('id', $selectedDepartmentId)
            : null;

        $departmentEmployees = Employee::with('user')
            ->where('company_id', $manager->company_id)
            ->where('id', '!=', $manager->id)
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

        return response()->json([
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartment,
            'selectedDepartmentId' => $selectedDepartmentId,
            'departmentEmployees' => $departmentEmployees,
            'notificationTypes' => config('ems.department_notifications.types', []),
        ]);
    }

    public function sendDepartmentNotification(DepartmentNotificationRequest $request): JsonResponse
    {
        $manager = $this->managerEmployee();
        $user = Auth::user();
        if (! $manager || ! $user) {
            abort(403);
        }

        $validated = $request->validated();

        $department = Department::where('id', $validated['department_id'])
            ->where('company_id', $manager->company_id)
            ->where('manager_employee_id', $manager->id)
            ->firstOrFail();

        $typeLabel = data_get(config('ems.department_notifications.types', []), $validated['type'], 'General Announcement');

        $employeeQuery = Employee::with('user')
            ->where('company_id', $manager->company_id)
            ->where('id', '!=', $manager->id)
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
            return response()->json([
                'message' => 'No valid employees were found for the selected department.',
                'errors' => ['employee_ids' => ['No valid employees were found for the selected department.']],
            ], 422);
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
            'sender_name' => $manager->user?->name,
        ];

        try {
            Notification::send($recipients, new DepartmentBroadcastNotification($manager, $department, $payload));
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Notification queued could not be sent. Please retry.',
            ], 500);
        }

        app(ActivityLogService::class)->log(
            $manager->company_id,
            $user->id,
            'department.notification.sent',
            "Department notification sent: {$validated['title']}",
            $department,
            ['recipient_mode' => $validated['recipient_mode'], 'recipient_count' => $recipients->count()]
        );

        return response()->json([
            'message' => 'Department notification sent successfully.',
            'recipient_count' => $recipients->count(),
        ]);
    }
}
