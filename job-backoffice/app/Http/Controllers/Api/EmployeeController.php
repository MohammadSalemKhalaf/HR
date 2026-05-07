<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Employee::with(['user', 'company', 'department', 'manager'])->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->string('company_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->string('department_id'));
        }

        return $this->success('Employees retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'exists:users,id', 'unique:employees,user_id'],
            'name' => ['required_without:user_id', 'string', 'max:255'],
            'email' => ['required_without:user_id', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required_without:user_id', 'string', 'min:8'],
            'company_id' => ['required', 'exists:companies,id'],
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

        $employee = DB::transaction(function () use ($request) {
            $user = null;

            if ($request->filled('user_id')) {
                $user = User::findOrFail($request->string('user_id'));
                if ($user->role !== 'employee') {
                    $user->forceFill(['role' => 'employee'])->save();
                }
            } else {
                $user = User::create([
                    'name' => $request->string('name'),
                    'email' => $request->string('email'),
                    'password' => Hash::make($request->string('password')),
                    'role' => 'employee',
                ]);
            }

            return Employee::create([
                'user_id' => $user->id,
                'company_id' => $request->string('company_id'),
                'department_id' => $request->input('department_id'),
                'employee_number' => $request->input('employee_number'),
                'job_title' => $request->input('job_title'),
                'hired_at' => $request->input('hired_at'),
                'status' => $request->input('status', 'probation'),
                'manager_id' => $request->input('manager_id'),
            ]);
        });

        return $this->success('Employee created successfully.', $employee->load(['user', 'company', 'department', 'manager']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $employee = Employee::with(['user', 'company', 'department', 'manager'])->find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        return $this->success('Employee retrieved successfully.', $employee);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
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

        $employee->update(array_filter([
            'user_id' => $request->input('user_id'),
            'company_id' => $request->input('company_id'),
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

        $employee->update(['status' => 'terminated']);

        return $this->success('Employee terminated successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }

    public function assignManager(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $validator = Validator::make($request->all(), [
            'manager_id' => ['required', 'exists:employees,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $employee->update(['manager_id' => $request->string('manager_id')]);

        return $this->success('Employee manager assigned successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }

    public function transferDepartment(Request $request, string $id): JsonResponse
    {
        $employee = Employee::find($id);

        if (! $employee) {
            return $this->notFound('Employee not found.');
        }

        $validator = Validator::make($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $employee->update(['department_id' => $request->string('department_id')]);

        return $this->success('Employee department transferred successfully.', $employee->fresh(['user', 'company', 'department', 'manager']));
    }
}
