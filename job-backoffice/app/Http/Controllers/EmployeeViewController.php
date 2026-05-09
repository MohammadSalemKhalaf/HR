<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\Leave;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EmployeeViewController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user', 'company', 'department')->latest()->paginate(15);

        return view('employee.index', compact('employees'));
    }

    public function show(string $id)
    {
        $employee = Employee::with(['user', 'company', 'department', 'attendanceRecords' => function($q){ $q->latest(); }])->findOrFail($id);

        return view('employee.show', compact('employee'));
    }

    public function checkIn(Request $request, string $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        $today = now()->toDateString();

        $record = AttendanceRecord::firstOrCreate(
            ['employee_id' => $employee->id, 'attendance_date' => $today],
            ['status' => 'present']
        );

        if ($record->check_in_at) {
            return Redirect::back()->with('error', 'Already checked in today.');
        }

        $record->check_in_at = now();
        $record->status = 'present';
        $record->save();

        return Redirect::back()->with('success', 'Checked in successfully.');
    }

    public function checkOut(Request $request, string $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $today = now()->toDateString();

        $record = AttendanceRecord::where('employee_id', $employee->id)->where('attendance_date', $today)->first();

        if (! $record || ! $record->check_in_at) {
            return Redirect::back()->with('error', 'No check-in found for today.');
        }

        if ($record->check_out_at) {
            return Redirect::back()->with('error', 'Already checked out today.');
        }

        $record->check_out_at = now();
        $record->save();

        return Redirect::back()->with('success', 'Checked out successfully.');
    }

    public function leaveIndex(Request $request)
    {
        $leaves = Leave::with(['employee.user', 'requestedBy', 'approvedBy'])->latest()->paginate(15);

        return view('leave.index', compact('leaves'));
    }

    public function leaveCreate(string $employeeId = null)
    {
        return view('leave.create', ['employeeId' => $employeeId]);
    }

    public function leaveStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'days_count' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $daysCount = $data['days_count'] ?? (now()->parse($data['start_date'])->diffInDays(now()->parse($data['end_date'])) + 1);

        $leave = Leave::create([
            'employee_id' => $data['employee_id'],
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days_count' => $daysCount,
            'status' => 'pending',
            'requested_by_user_id' => $request->user()->id,
        ]);

        return Redirect::route('leave.index')->with('success', 'Leave request submitted.');
    }
}
