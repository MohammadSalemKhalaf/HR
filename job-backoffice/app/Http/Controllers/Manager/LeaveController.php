<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use App\Services\ActivityLogService;
use App\Notifications\LeaveApproved;
use App\Notifications\LeaveRejected;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        $deptIds = Department::where('manager_employee_id', $manager->id)->pluck('id');
        $employeeIds = Employee::whereIn('department_id', $deptIds)->pluck('id');

        $leaves = Leave::with(['employee.user', 'requestedBy', 'approvedBy'])
            ->whereIn('employee_id', $employeeIds)
            ->latest()
            ->paginate(15);

        return view('manager.leaves.index', compact('leaves'));
    }

    public function approve(Request $request, Leave $leave)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        // ensure leave belongs to employee in manager's departments
        $deptIds = Department::where('manager_employee_id', $manager->id)->pluck('id');
        $employeeIds = Employee::whereIn('department_id', $deptIds)->pluck('id');

        if (! in_array($leave->employee_id, $employeeIds->toArray())) {
            abort(403);
        }

        $leave->status = 'approved';
        $leave->approved_by_user_id = $user->id;
        $leave->approved_at = now();
        $leave->save();

        app(ActivityLogService::class)->log($manager->company_id ?? null, $user->id, 'leave.approved', "Leave approved for {$leave->employee->user?->name}", $leave, ['leave_id' => $leave->id]);

        // Send approval notification to employee
        try {
            if ($leave->employee->user) {
                Notification::send($leave->employee->user, new LeaveApproved($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return Redirect::back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        $deptIds = Department::where('manager_employee_id', $manager->id)->pluck('id');
        $employeeIds = Employee::whereIn('department_id', $deptIds)->pluck('id');

        if (! in_array($leave->employee_id, $employeeIds->toArray())) {
            abort(403);
        }

        $leave->status = 'rejected';
        $leave->approved_by_user_id = $user->id;
        $leave->approved_at = now();
        $leave->save();

        app(ActivityLogService::class)->log($manager->company_id ?? null, $user->id, 'leave.rejected', "Leave rejected for {$leave->employee->user?->name}", $leave, ['leave_id' => $leave->id]);

        // Send rejection notification to employee
        try {
            if ($leave->employee->user) {
                Notification::send($leave->employee->user, new LeaveRejected($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return Redirect::back()->with('success', 'Leave rejected.');
    }
}
