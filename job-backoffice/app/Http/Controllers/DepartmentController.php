<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId) {
            abort(403);
        }

        $departments = Department::where('company_id', $companyId)
            ->with(['manager.user', 'company'])
            ->latest()
            ->paginate(15);

        return view('department.index', compact('departments', 'companyId'));
    }

    public function create()
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId) {
            abort(403);
        }

        $employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->where('role_id', User::roleIdFor('manager'));
            })
            ->with('user')
            ->get();

        return view('department.create', compact('companyId', 'employees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $manager = $this->resolveManagerEmployee($validated['manager_employee_id'] ?? null, $companyId);

        $department = Department::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'code' => $validated['code'],
            'manager_employee_id' => $manager?->id,
        ]);

        return redirect()->route('departments.show', $department->id)->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        $department->load(['manager.user', 'company']);
        $employees = Employee::where('department_id', $department->id)
            ->with('user', 'manager')
            ->latest()
            ->paginate(10);

        $availableEmployees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->where(function ($query) use ($department) {
                $query->whereNull('department_id')
                    ->orWhere('department_id', '!=', $department->id);
            })
            ->with('user')
            ->get();

        return view('department.show', compact('department', 'employees', 'availableEmployees'));
    }

    public function edit(Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        $employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->where('role_id', User::roleIdFor('manager'));
            })
            ->with('user')
            ->get();

        return view('department.edit', compact('department', 'employees', 'companyId'));
    }

    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        $manager = $this->resolveManagerEmployee($validated['manager_employee_id'] ?? null, $companyId);

        $department->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'manager_employee_id' => $manager?->id,
        ]);

        return redirect()->route('departments.show', $department->id)->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        if ($department->manager_employee_id) {
            return back()->withErrors(['general' => 'Cannot delete department with assigned manager. Please reassign first.']);
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }

    public function assignManager(Request $request, Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'manager_employee_id' => ['required', 'exists:employees,id'],
        ]);

        $manager = $this->resolveManagerEmployee($validated['manager_employee_id'], $companyId);

        $department->update(['manager_employee_id' => $manager->id]);

        return back()->with('success', 'Manager assigned successfully.');
    }

    public function transferEmployee(Request $request, Department $department)
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId || $department->company_id !== $companyId) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (!$employee || $employee->company_id !== $companyId) {
            return back()->withErrors(['employee_id' => 'Employee must be from your company.']);
        }

        $employee->update(['department_id' => $department->id]);

        return back()->with('success', 'Employee transferred successfully.');
    }

    private function resolveManagerEmployee(?string $managerEmployeeId, string $companyId): ?Employee
    {
        if (!$managerEmployeeId) {
            return null;
        }

        $manager = Employee::with('user')->find($managerEmployeeId);

        if (!$manager || $manager->company_id !== $companyId) {
            throw ValidationException::withMessages([
                'manager_employee_id' => 'Manager must be from your company.',
            ]);
        }

        if ($manager->status !== 'active') {
            throw ValidationException::withMessages([
                'manager_employee_id' => 'Manager must be active.',
            ]);
        }

        if (!$manager->user?->hasRole('manager')) {
            throw ValidationException::withMessages([
                'manager_employee_id' => 'Selected employee must have the manager role.',
            ]);
        }

        return $manager;
    }
}
