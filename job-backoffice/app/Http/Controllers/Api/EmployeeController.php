<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmployeeController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Employee::with(['user', 'company', 'department', 'manager'])->latest();
        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($companyId === '') {
                return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
            }

            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->string('company_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->string('department_id'));
        }

        return $this->success('Employees retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $actingUser = $request->user();

        $companyRule = ['required', 'exists:companies,id'];

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyRule = ['nullable', 'exists:companies,id'];
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['required_without:user_id', 'string', 'max:255'],
            'email' => ['required_without:user_id', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required_without:user_id', 'string', 'min:8'],
            'role' => ['nullable', Rule::in(['employee', 'manager'])],
            'company_id' => $companyRule,
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_number' => ['nullable', 'string', 'unique:employees,employee_number'],
            'job_title' => ['nullable', 'string'],
            'hired_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,terminated,probation'],
            'manager_id' => ['nullable', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        if ($actingUser instanceof User && $actingUser->hasRole('company') && ! ($actingUser->company?->id ?? $actingUser->employee?->company_id)) {
            return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
        }

        $resolvedCompanyId = $request->string('company_id')->toString();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $resolvedCompanyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        }

        if ($request->filled('department_id')) {
            $department = Department::find($request->string('department_id'));

            if (! $department || $department->company_id !== $resolvedCompanyId) {
                return $this->error('Validation failed.', ['department_id' => ['Department must belong to the selected company.']], 422);
            }
        }

        if ($request->filled('manager_id')) {
            $manager = Employee::find($request->string('manager_id'));

            if (! $manager || $manager->company_id !== $resolvedCompanyId) {
                return $this->error('Validation failed.', ['manager_id' => ['Manager must belong to the selected company.']], 422);
            }
        }

        $employee = DB::transaction(function () use ($request) {
            $roleSlug = $request->input('role', 'employee');
            $actingUser = $request->user();
            $companyId = $request->string('company_id')->toString();

            if ($actingUser instanceof User && $actingUser->hasRole('company')) {
                $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
            }

            $user = null;

            if ($request->filled('user_id')) {
                $user = User::findOrFail($request->string('user_id'));

                $existingEmployee = Employee::where('user_id', $user->id)->first();

                if ($existingEmployee && $existingEmployee->company_id !== $companyId) {
                    throw ValidationException::withMessages([
                        'user_id' => ['Selected user already belongs to another company.'],
                    ]);
                }
            } else {
                $user = User::create([
                    'name' => $request->string('name'),
                    'email' => $request->string('email'),
                    'password' => Hash::make($request->string('password')),
                    'role_id' => User::roleIdFor($roleSlug),
                ]);
            }

            return Employee::syncForUser($user, [
                'user_id' => $user->id,
                'company_id' => $companyId,
                'department_id' => $request->input('department_id'),
                'employee_number' => $request->input('employee_number'),
                'job_title' => $request->input('job_title'),
                'hired_at' => $request->input('hired_at'),
                'status' => $request->input('status', 'probation'),
                'manager_id' => $request->input('manager_id'),
            ], $roleSlug);
        });

        return $this->success('Employee created successfully.', $employee->load(['user', 'company', 'department', 'manager']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = Employee::with(['user', 'company', 'department', 'manager'])->find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $actingUser = request()->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($employee->company_id !== $companyId) {
                return $this->notFound('Employee not found.');
            }
        }

        return $this->success('Employee retrieved successfully.', $employee);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($employee->company_id !== $companyId) {
                return $this->notFound('Employee not found.');
            }
        }

        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'exists:users,id', 'unique:employees,user_id,'.$employee->id.',id'],
            'company_id' => ['sometimes', 'exists:companies,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_number' => ['nullable', 'string', 'unique:employees,employee_number,'.$employee->id.',id'],
            'job_title' => ['nullable', 'string'],
            'hired_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,terminated,probation'],
            'manager_id' => ['nullable', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $targetCompanyId = $request->input('company_id', $employee->company_id);

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $targetCompanyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        }

        if ($request->filled('department_id')) {
            $department = Department::find($request->string('department_id'));

            if (! $department || $department->company_id !== $targetCompanyId) {
                return $this->error('Validation failed.', ['department_id' => ['Department must belong to the selected company.']], 422);
            }
        }

        if ($request->filled('manager_id')) {
            $manager = Employee::find($request->string('manager_id'));

            if (! $manager || $manager->company_id !== $targetCompanyId) {
                return $this->error('Validation failed.', ['manager_id' => ['Manager must belong to the selected company.']], 422);
            }
        }

        $employee->update(array_filter([
            'user_id' => $request->input('user_id'),
            'company_id' => $targetCompanyId,
            'department_id' => $request->input('department_id'),
            'employee_number' => $request->input('employee_number'),
            'job_title' => $request->input('job_title'),
            'hired_at' => $request->input('hired_at'),
            'status' => $request->input('status'),
            'manager_id' => $request->input('manager_id'),
        ], static fn ($value) => $value !== null));

        return $this->success('Employee updated successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }

    public function terminate(string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $actingUser = request()->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($employee->company_id !== $companyId) {
                return $this->notFound('Employee not found.');
            }
        }

        $employee->update(['status' => 'terminated']);

        return $this->success('Employee terminated successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }

    public function assignManager(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($employee->company_id !== $companyId) {
                return $this->notFound('Employee not found.');
            }
        }

        $validator = Validator::make($request->all(), [
            'manager_id' => ['required', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $manager = Employee::find($request->string('manager_id'));

        if (! $manager || $manager->company_id !== $employee->company_id) {
            return $this->error('Validation failed.', ['manager_id' => ['Manager must belong to the same company.']], 422);
        }

        $employee->update(['manager_id' => $manager->id]);

        return $this->success('Employee manager assigned successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }

    public function transferDepartment(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');

            if ($employee->company_id !== $companyId) {
                return $this->notFound('Employee not found.');
            }
        }

        $validator = Validator::make($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $department = Department::find($request->string('department_id'));

        if (! $department || $department->company_id !== $employee->company_id) {
            return $this->error('Validation failed.', ['department_id' => ['Department must belong to the same company.']], 422);
        }

        $employee->update(['department_id' => $department->id]);

        return $this->success('Employee department transferred successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }
}
