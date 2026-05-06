<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'exists:users,id'],
            'company_id' => ['required', 'exists:companies,id'],
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

        $validated = $validator->validated();

        $employee = Employee::create([
            'user_id' => $validated['user_id'] ?? null,
            'company_id' => $validated['company_id'],
            'department_id' => $validated['department_id'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'hired_at' => $validated['hired_at'] ?? null,
            'status' => $validated['status'] ?? 'probation',
            'manager_id' => $validated['manager_id'] ?? null,
        ]);

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
