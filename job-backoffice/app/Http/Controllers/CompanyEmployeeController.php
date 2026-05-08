<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompanyEmployeeController extends Controller
{
    private function companyIdForCurrentUser(): ?string
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return null;
        }

        return $user->company?->id
            ?? Company::where('ownerId', $user->id)->value('id')
            ?? $user->employee?->company_id;
    }

    private function canAccessEmployee(Employee $employee): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $companyId = $this->companyIdForCurrentUser();

        return (bool) $companyId && $employee->company_id === $companyId;
    }

    private function resolveCompanyDepartment(?string $departmentId, string $companyId): ?Department
    {
        if (!$departmentId) {
            return null;
        }

        $department = Department::find($departmentId);

        if (!$department || $department->company_id !== $companyId) {
            throw ValidationException::withMessages([
                'department_id' => 'Invalid department.',
            ]);
        }

        return $department;
    }

    private function preventUnsafeDowngrade(Employee $employee, ?string $roleType): void
    {
        if ($roleType !== 'employee') {
            return;
        }

        if (!$employee->user?->hasRole('manager')) {
            return;
        }

        if (Department::where('manager_employee_id', $employee->id)->exists()) {
            throw ValidationException::withMessages([
                'role_type' => 'Reassign this manager from all departments before downgrading their role.',
            ]);
        }
    }

    private function resolveAccessibleEmployee(string $employeeId): Employee
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403);
        }

        $query = Employee::with(['user', 'company', 'department', 'manager.user'])
            ->where('id', $employeeId);

        if (! $user->hasRole('admin')) {
            $companyId = $this->companyIdForCurrentUser();

            if (!$companyId) {
                abort(403);
            }

            $query->where('company_id', $companyId);
        }

        return $query->firstOrFail();
    }

    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId) {
            abort(403);
        }

        $employees = Employee::where('company_id', $companyId)
            ->with(['user', 'department', 'manager.user'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Employee::where('company_id', $companyId)->count(),
            'active' => Employee::where('company_id', $companyId)->where('status', 'active')->count(),
            'managers' => Employee::where('company_id', $companyId)
                ->whereHas('user', fn($q) => $q->where('role_id', User::roleIdFor('manager')))
                ->count(),
        ];

        return view('company-employee.index', compact('employees', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        $companyId = $user->company?->id ?? $user->employee?->company_id;

        if (!$companyId) {
            abort(403);
        }

        $departments = Department::where('company_id', $companyId)->get();

        return view('company-employee.create', compact('companyId', 'departments'));
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_type' => ['required', Rule::in(['employee', 'manager'])],
            'department_id' => ['nullable', 'exists:departments,id'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hired_at' => ['nullable', 'date'],
        ]);

        if ($validated['department_id']) {
            $dept = Department::find($validated['department_id']);
            if (!$dept || $dept->company_id !== $companyId) {
                return back()->withErrors(['department_id' => 'Invalid department.']);
            }
        }

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => User::roleIdFor($validated['role_type']),
        ]);

        $employee = Employee::syncForUser($newUser, [
            'company_id' => $companyId,
            'department_id' => $validated['department_id'],
            'job_title' => $validated['job_title'],
            'salary' => $validated['salary'] ?? null,
            'hired_at' => $validated['hired_at'] ?? now(),
            'status' => 'active',
        ], $validated['role_type']);

        return redirect()->route('company-employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(string $company_employee)
    {
        $employee = $this->resolveAccessibleEmployee($company_employee);
        $companyId = $employee->company_id;
        $departments = Department::where('company_id', $companyId)->get();

        return view('company-employee.show', compact('employee', 'departments'));
    }

    public function edit(string $company_employee)
    {
        $employee = $this->resolveAccessibleEmployee($company_employee);
        $companyId = $employee->company_id;
        $departments = Department::where('company_id', $companyId)->get();

        return view('company-employee.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, string $company_employee)
    {
        $employee = $this->resolveAccessibleEmployee($company_employee);
        $companyId = $employee->company_id;

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'role_type' => ['nullable', Rule::in(['employee', 'manager'])],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hired_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['active', 'terminated', 'probation'])],
        ]);

        $department = $this->resolveCompanyDepartment($validated['department_id'] ?? null, $companyId);

        $this->preventUnsafeDowngrade($employee, $validated['role_type'] ?? null);

        if ($validated['name'] && $employee->user) {
            $employee->user->update(['name' => $validated['name']]);
        }

        if ($validated['role_type'] && $employee->user) {
            $employee->user->forceFill(['role_id' => User::roleIdFor($validated['role_type'])])->save();
        }

        $employee->update([
            'job_title' => $validated['job_title'] ?? null,
            'department_id' => $department?->id,
            'salary' => $validated['salary'] ?? null,
            'hired_at' => $validated['hired_at'] ?? null,
            'status' => $validated['status'] ?? null,
        ]);

        return redirect()->route('company-employees.show', $employee->id)
            ->with('success', 'Employee updated successfully.');
    }

    public function transferDepartment(Request $request, string $company_employee)
    {
        $employee = $this->resolveAccessibleEmployee($company_employee);
        $companyId = $employee->company_id;

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $dept = $this->resolveCompanyDepartment($validated['department_id'], $companyId);

        $employee->update(['department_id' => $dept->id]);

        return back()->with('success', 'Employee transferred successfully.');
    }

    public function destroy(string $company_employee)
    {
        $employee = $this->resolveAccessibleEmployee($company_employee);

        $employee->delete();

        return redirect()->route('company-employees.index')->with('success', 'Employee archived successfully.');
    }
}
