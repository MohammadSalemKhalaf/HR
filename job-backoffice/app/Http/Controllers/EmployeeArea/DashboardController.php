<?php

namespace App\Http\Controllers\EmployeeArea;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\Leave;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->with('department', 'manager')->firstOrFail();

        $today = now()->toDateString();
        $todayRecord = AttendanceRecord::where('employee_id', $employee->id)->where('attendance_date', $today)->first();

        $monthHours = AttendanceRecord::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', now()->month)
            ->get()
            ->map(function($r){
                if ($r->check_in_at && $r->check_out_at) {
                    return $r->check_out_at->diffInMinutes($r->check_in_at);
                }
                return 0;
            })->sum();

        $leaveCount = Leave::where('employee_id', $employee->id)->count();

        return view('employee.dashboard', compact('employee', 'todayRecord', 'monthHours', 'leaveCount'));
    }
}
