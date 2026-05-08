<?php

namespace App\Http\Controllers\EmployeeArea;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\ActivityLogService;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $records = AttendanceRecord::where('employee_id', $employee->id)->latest()->paginate(15);

        return view('employee.attendance.index', compact('records'));
    }

    public function checkIn(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $today = now()->toDateString();

        $existing = AttendanceRecord::where('employee_id', $employee->id)->where('attendance_date', $today)->first();

        if ($existing && $existing->check_in_at) {
            return Redirect::back()->with('error', 'Already checked in today.');
        }

        $record = $existing ?? new AttendanceRecord(['employee_id' => $employee->id, 'attendance_date' => $today]);
        $record->check_in_at = now();
        $record->status = 'present';
        $record->save();

        app(ActivityLogService::class)->log($employee->company_id, $user->id, 'attendance.check_in', "Check-in by {$user->name}", $record, ['attendance_date' => $today]);

        return Redirect::back()->with('success', 'Checked in successfully.');
    }

    public function checkOut(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

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

        app(ActivityLogService::class)->log($employee->company_id, $user->id, 'attendance.check_out', "Check-out by {$user->name}", $record, ['attendance_date' => $today]);

        return Redirect::back()->with('success', 'Checked out successfully.');
    }
}
