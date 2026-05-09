<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        $employees = Employee::with('user', 'department')
            ->whereIn('department_id', Department::where('manager_employee_id', $manager->id)->pluck('id'))
            ->paginate(15);

        return view('manager.employees.index', compact('employees'));
    }

    public function show(Request $request, Employee $employee)
    {
        $user = $request->user();
        $manager = Employee::where('user_id', $user->id)->firstOrFail();

        if (! in_array($employee->department_id, Department::where('manager_employee_id', $manager->id)->pluck('id')->toArray())) {
            abort(403);
        }

        $employee->load('user', 'department', 'attendanceRecords');

        return view('manager.employees.show', compact('employee'));
    }
}
