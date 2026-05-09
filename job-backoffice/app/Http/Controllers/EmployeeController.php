<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        $employees = Employee::with(['user', 'company', 'department', 'manager'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully.',
            'data' => $employees,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $actingUser = Auth::user();

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
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($actingUser instanceof User && $actingUser->hasRole('company') && ! ($actingUser->company?->id ?? $actingUser->employee?->company_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => ['company_id' => ['Company could not be resolved from token.']],
            ], 422);
        }

        $validated = $validator->validated();

        $roleSlug = $validated['role'] ?? 'employee';
        $companyId = $validated['company_id'];

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            $companyId = (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        }

        $user = isset($validated['user_id'])
            ? User::findOrFail($validated['user_id'])
            : User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => User::roleIdFor($roleSlug),
            ]);

        $employee = Employee::syncForUser($user, [
            'company_id' => $companyId,
            'department_id' => $validated['department_id'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'hired_at' => $validated['hired_at'] ?? null,
            'status' => $validated['status'] ?? 'probation',
            'manager_id' => $validated['manager_id'] ?? null,
        ], $roleSlug);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'data' => $employee->load(['user', 'company', 'department', 'manager']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $employee = Employee::with(['user', 'company', 'department', 'manager'])
                ->findOrFail($id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.',
                'errors' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee retrieved successfully.',
            'data' => $employee,
        ]);
    }
}
