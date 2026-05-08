<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (! $employee) {
            abort(403);
        }

        $managedDepartments = Department::where('manager_employee_id', $employee->id)->get();

        $departmentsCount = $managedDepartments->count();

        $employeesCount = Employee::whereIn('department_id', $managedDepartments->pluck('id'))->count();

        $pendingLeaves = Leave::whereIn('employee_id', Employee::whereIn('department_id', $managedDepartments->pluck('id'))->pluck('id'))
            ->where('status', 'pending')
            ->count();

        $today = now()->toDateString();
        $todayAttendance = AttendanceRecord::whereIn('employee_id', Employee::whereIn('department_id', $managedDepartments->pluck('id'))->pluck('id'))
            ->where('attendance_date', $today)
            ->count();

        return view('manager.dashboard', compact('departmentsCount', 'employeesCount', 'pendingLeaves', 'todayAttendance'));
    }
}
