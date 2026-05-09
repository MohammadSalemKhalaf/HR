<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class ManagerTaskController extends Controller
{
    private function managerEmployeeId(): ?string
    {
        $user = Auth::user();

        return $user?->employee?->id;
    }

    private function managedDepartmentIds(): array
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            return [];
        }

        return Department::where('manager_employee_id', $managerId)
            ->pluck('id')
            ->map(fn ($value) => (string) $value)
            ->toArray();
    }

    public function index(): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $tasks = EmployeeTask::where('manager_employee_id', $managerId)
            ->with(['employee.user', 'department'])
            ->latest()
            ->paginate(15);

        return response()->json($tasks);
    }

    public function employees(): JsonResponse
    {
        $deptIds = $this->managedDepartmentIds();

        $employees = Employee::whereIn('department_id', $deptIds)
            ->with(['user', 'department'])
            ->get();

        return response()->json($employees);
    }

    public function show(string $id): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $task = EmployeeTask::with(['employee.user', 'department'])->findOrFail($id);
        if ($task->manager_employee_id !== $managerId) {
            abort(403);
        }

        return response()->json($task);
    }

    public function store(Request $request): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $deptIds = $this->managedDepartmentIds();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'repository_url' => ['nullable', 'url'],
            'priority' => ['required', 'in:low,medium,high'],
            'employee_id' => ['required', 'exists:employees,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (! $employee || ! in_array($employee->department_id, $deptIds)) {
            abort(403);
        }

        $task = EmployeeTask::create([
            'company_id' => $employee->company_id,
            'department_id' => $employee->department_id,
            'manager_employee_id' => $managerId,
            'employee_id' => $employee->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'repository_url' => $validated['repository_url'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'task' => $task->load(['employee.user', 'department']),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $task = EmployeeTask::findOrFail($id);
        if ($task->manager_employee_id !== $managerId) {
            abort(403);
        }

        $deptIds = $this->managedDepartmentIds();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'repository_url' => ['nullable', 'url'],
            'priority' => ['required', 'in:low,medium,high'],
            'employee_id' => ['required', 'exists:employees,id'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        $employee = Employee::find($validated['employee_id']);
        if (! $employee || ! in_array($employee->department_id, $deptIds)) {
            abort(403);
        }

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'repository_url' => $validated['repository_url'] ?? null,
            'priority' => $validated['priority'],
            'employee_id' => $employee->id,
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => $task->fresh(['employee.user', 'department']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $managerId = $this->managerEmployeeId();
        if (! $managerId) {
            abort(403);
        }

        $task = EmployeeTask::findOrFail($id);
        if ($task->manager_employee_id !== $managerId) {
            abort(403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}
