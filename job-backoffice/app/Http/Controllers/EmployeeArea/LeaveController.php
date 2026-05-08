<?php

namespace App\Http\Controllers\EmployeeArea;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use App\Services\ActivityLogService;
use App\Notifications\LeaveRequested;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $leaves = Leave::with('employee')->where('employee_id', $employee->id)->latest()->paginate(15);

        return view('employee.leaves.index', compact('leaves'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        return view('employee.leaves.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $data = $request->validate([
            'leave_type' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);

        $daysCount = now()->parse($data['start_date'])->diffInDays(now()->parse($data['end_date'])) + 1;

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days_count' => $daysCount,
            'status' => 'pending',
            'requested_by_user_id' => $user->id,
        ]);

        app(ActivityLogService::class)->log($employee->company_id, $user->id, 'leave.requested', "Leave requested by {$user->name}: {$data['leave_type']} ({$data['start_date']} - {$data['end_date']})", null, ['employee_id' => $employee->id, 'days' => $daysCount]);

        // Notify manager of leave request
        try {
            if ($employee->manager && $employee->manager->user) {
                Notification::send($employee->manager->user, new LeaveRequested($leave));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return Redirect::route('employee.leaves.index')->with('success', 'Leave requested.');
    }

    public function show(Request $request, Leave $leave)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        if ($leave->employee_id !== $employee->id) {
            abort(403);
        }

        return view('employee.leaves.show', compact('leave'));
    }
}
