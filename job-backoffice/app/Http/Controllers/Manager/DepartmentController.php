<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $departments = Department::where('manager_employee_id', $employee->id)->paginate(15);

        return view('manager.departments.index', compact('departments'));
    }

    public function show(Request $request, Department $department)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        if ($department->manager_employee_id !== $employee->id) {
            abort(403);
        }

        $employees = Employee::with('user')->where('department_id', $department->id)->paginate(15);

        return view('manager.departments.show', compact('department', 'employees'));
    }
}
