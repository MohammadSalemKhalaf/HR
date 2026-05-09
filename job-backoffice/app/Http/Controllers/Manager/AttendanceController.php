<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        $deptIds = Department::where('manager_employee_id', $manager->id)->pluck('id');

        $employeeIds = Employee::whereIn('department_id', $deptIds)->pluck('id');

        $date = $request->date('date') ?? now()->toDateString();

        $records = AttendanceRecord::with('employee.user')
            ->whereIn('employee_id', $employeeIds)
            ->where('attendance_date', $date)
            ->get();

        return view('manager.attendance.index', compact('records', 'date'));
    }
}
